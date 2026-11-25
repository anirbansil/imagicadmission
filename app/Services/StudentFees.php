<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ApplicationFormEmi;
use App\Models\StudentPayment;
use App\Models\User;
use Carbon\Carbon;

/**
 * A student's course fees: the admission payment taken by the center and
 * the EMI schedule (application_form_emi) with each instalment's status.
 *
 * An EMI is paid when the center (or an online payment) has set its
 * payment_type to Cash, Cheque or Online, the same rule the admin
 * receipt pages use.
 */
class StudentFees
{
    const PAID_TYPES = ['Cash', 'Cheque', 'Online'];

    public static function isPaid($emi)
    {
        return in_array($emi->payment_type, self::PAID_TYPES, true);
    }

    public static function today()
    {
        return Carbon::now(config('attendance.timezone'))->startOfDay();
    }

    /**
     * Late fee for an unpaid EMI: a fixed amount per day after the due date,
     * only when the center has switched the extra amount on for that EMI.
     */
    public static function lateFee($emi, $today = null)
    {
        if ($emi->extra_amount_add !== 'on') {
            return 0;
        }
        $due = StudentEnrolments::date($emi->emi_dates);
        $today = $today ?: self::today();
        if (!$due || $due->gte($today)) {
            return 0;
        }
        return $due->diffInDays($today) * (int) config('payment.late_fee_per_day');
    }

    public static function gatewayReady()
    {
        return config('payment.razorpay.key_id') && config('payment.razorpay.key_secret');
    }

    /**
     * Whether students of this center may pay online.
     */
    public static function onlineAllowed($centerId)
    {
        $centers = config('payment.online_centers');
        return self::gatewayReady() && (empty($centers) || in_array((string) $centerId, $centers, true));
    }

    /**
     * Every enrolment of the student with its fee account.
     *
     * @return \Illuminate\Support\Collection of {enrolment, application, center_id, online,
     *   total, admission, emis, paid, balance, overdue, next}
     */
    public static function for($user)
    {
        $today = self::today();
        $enrolments = StudentEnrolments::for($user);
        $all = $enrolments->current->concat($enrolments->completed);

        $emis = ApplicationFormEmi::whereIn('application_id', $all->pluck('application.id'))
            ->orderBy('emi_no')->orderBy('emi_dates')->orderBy('id')
            ->get()->groupBy('application_id');
        // Instalments the student paid through the Student Portal. Every other
        // paid instalment was recorded by the center / admin (cash, cheque or
        // an online payment they took), including all older records.
        $online = StudentPayment::whereIn('emi_id', $emis->flatten()->pluck('id'))
            ->where('status', StudentPayment::PAID)->get()->keyBy('emi_id');
        $staff = User::whereIn('id', $emis->flatten()->pluck('updated_by')->filter()->unique())->get()->keyBy('id');

        return $all->map(function ($e) use ($enrolments, $emis, $online, $staff, $today) {
            $application = $e->application;
            $registration = $enrolments->registrations->get($application->reg_id);
            $centerId = optional($registration)->center_name;

            $rows = $emis->get($application->id, collect())->values()->map(function ($emi, $i) use ($online, $staff, $e, $today) {
                $row = self::row($emi, $i + 1, $online->get($emi->id), $today);
                if ($row->channel === 'center') {
                    $row->collected_by = self::collector($staff->get($emi->updated_by), $e->center);
                }
                return $row;
            });
            $next = $rows->first(function ($r) { return $r->status !== 'paid'; });
            $canPay = self::onlineAllowed($centerId);
            if ($next) {
                $next->payable = $canPay;
            }

            return (object) [
                'enrolment' => $e,
                'application' => $application,
                'center_id' => $centerId,
                'online' => $canPay,
                'total' => (int) $application->net_total,
                'admission' => (int) $application->pay_on_admission,
                'emis' => $rows,
                'paid' => $rows->where('status', 'paid')->sum('paid_amount'),
                'paid_center' => $rows->where('channel', 'center')->sum('paid_amount'),
                'paid_portal' => $rows->where('channel', 'portal')->sum('paid_amount'),
                'balance' => $rows->where('status', '!=', 'paid')->sum('amount'),
                'overdue' => $rows->where('status', 'overdue')->count(),
                'next' => $next,
            ];
        })->values();
    }

    /**
     * One instalment for display.
     */
    private static function row($emi, $number, $online, $today)
    {
        $due = StudentEnrolments::date($emi->emi_dates);
        $paid = self::isPaid($emi);
        $status = $paid ? 'paid' : ($due && $due->lt($today) ? 'overdue' : 'pending');
        $lateFee = $paid ? (int) $emi->extra_amount : self::lateFee($emi, $today);

        return (object) [
            'emi' => $emi,
            'number' => $emi->emi_no ?: $number,
            'due' => $due,
            'amount' => (int) $emi->emi_amounts,
            'late_fee' => $lateFee,
            'payable_amount' => (int) $emi->emi_amounts + $lateFee,
            'status' => $status,
            'due_today' => !$paid && $due && $due->eq($today),
            'days_overdue' => $status === 'overdue' ? $due->diffInDays($today) : 0,
            'paid_on' => $paid ? StudentEnrolments::date($emi->update_date) : null,
            'paid_amount' => $paid ? (int) $emi->emi_amounts + (int) $emi->extra_amount : 0,
            'mode' => $paid ? $emi->payment_type : null,
            'reference' => $paid ? $emi->cheque_no : null,
            'receipt_no' => $paid ? (optional($online)->receipt_no ?: 'EMI-'.$emi->id) : null,
            'online' => $online,
            // Who took the payment: "portal" = the student paid online themselves,
            // "center" = recorded by the center / admin.
            'channel' => $paid ? ($online ? 'portal' : 'center') : null,
            'collected_by' => null,
            'payable' => false,
        ];
    }

    /**
     * Name shown for a payment recorded by staff: the center account that
     * recorded it, or the student's center when that account no longer exists.
     */
    private static function collector($user, $studentCenter)
    {
        if (!$user) {
            return $studentCenter ?: 'Center';
        }
        if ($user->is_admin == 1) {
            return 'Head Office';
        }
        return $user->branch_id ? (Helper::getCenterName($user->branch_id) ?: $user->name) : $user->name;
    }
}
