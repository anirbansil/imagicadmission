<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ApplicationForm;
use App\Models\ApplicationFormEmi;
use App\Models\RegistrationForm;
use App\Models\StudentPayment;
use App\Services\EmiPayments;
use App\Services\StudentFees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

/**
 * Student Portal → My Payments: EMI schedule, online EMI payment through
 * Razorpay, payment history and receipts. The admission payment is taken
 * by the center and is only shown here.
 */
class StudentPaymentController extends Controller
{
    public function index(EmiPayments $payments)
    {
        $user = $this->student();

        // Finish payments whose browser callback never arrived (tab closed after paying).
        if (StudentFees::gatewayReady()) {
            StudentPayment::where('user_id', $user->id)->where('status', StudentPayment::CREATED)
                ->where('created_at', '>=', now()->subDays(3))->get()
                ->each(function ($p) use ($payments) {
                    try {
                        $payments->reconcile($p);
                    } catch (\Exception $e) {
                        Log::warning('Could not check Razorpay order', ['payment' => $p->id, 'error' => $e->getMessage()]);
                    }
                });
        }

        $accounts = StudentFees::for($user);
        $history = $accounts->flatMap(function ($a) {
            return $a->emis->where('status', 'paid')->map(function ($r) use ($a) {
                return (object) ['row' => $r, 'account' => $a];
            });
        })->sortByDesc(function ($h) {
            return optional($h->row->paid_on)->timestamp.'-'.str_pad($h->row->emi->id, 10, '0', STR_PAD_LEFT);
        })->values();
        $refunds = StudentPayment::where('user_id', $user->id)->where('status', StudentPayment::DUPLICATE)->get();
        $title = 'My Payments';

        return view('student-payments.index', compact('title', 'user', 'accounts', 'history', 'refunds'));
    }

    /**
     * Create the Razorpay order for one instalment (AJAX).
     */
    public function order(EmiPayments $payments, $emiId)
    {
        $user = $this->student();
        try {
            $payment = $payments->start($user, (int) $emiId);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Could not create Razorpay order', ['emi' => $emiId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'The payment could not be started. Please try again in a few minutes.'], 503);
        }

        $application = ApplicationForm::find($payment->application_id);
        $registration = RegistrationForm::find($application->reg_id);
        $emi = ApplicationFormEmi::find($payment->emi_id);

        return response()->json([
            'key' => config('payment.razorpay.key_id'),
            'order_id' => $payment->order_id,
            'amount' => $payment->amount * 100,
            'currency' => $payment->currency,
            'name' => config('payment.merchant_name'),
            'description' => 'EMI '.$emi->emi_no.' · '.Helper::getCourseName($application->course),
            'prefill' => [
                'name' => $registration ? trim(preg_replace('/\s+/', ' ', $registration->first_name.' '.$registration->mid_name.' '.$registration->last_name)) : $user->name,
                'email' => $user->email,
                'contact' => optional($registration)->mobile_no,
            ],
            'notes' => ['application' => $application->application_id],
            'theme' => ['color' => config('payment.theme_color')],
        ]);
    }

    /**
     * Razorpay Checkout success callback.
     */
    public function verify(Request $request, EmiPayments $payments)
    {
        $user = $this->student();
        $payment = StudentPayment::where('user_id', $user->id)->where('order_id', (string) $request->razorpay_order_id)->firstOrFail();

        try {
            $payment = $payments->confirm($payment, (string) $request->razorpay_payment_id, (string) $request->razorpay_signature);
        } catch (\Exception $e) {
            Log::error('Could not confirm Razorpay payment', ['payment' => $payment->id, 'error' => $e->getMessage()]);
        }

        if ($payment->status === StudentPayment::PAID) {
            return redirect()->route('student.payments')->with('paid', $payment->emi_id)
                ->with('msg', 'Payment of ₹'.number_format($payment->amount).' received. Thank you! Your receipt number is '.$payment->receipt_no.'.');
        }
        if ($payment->status === StudentPayment::DUPLICATE) {
            return redirect()->route('student.payments')
                ->with('error', 'We received your payment, but this instalment had already been paid. Your center will refund it. Reference: '.$payment->payment_id);
        }
        return redirect()->route('student.payments')
            ->with('error', 'We could not confirm your payment yet. If money was deducted, it will show here automatically within a few minutes. Reference: '.$request->razorpay_payment_id);
    }

    /**
     * Razorpay Checkout reported a failure (AJAX).
     */
    public function failed(Request $request, EmiPayments $payments)
    {
        $user = $this->student();
        $payment = StudentPayment::where('user_id', $user->id)->where('order_id', (string) $request->order_id)->first();
        if ($payment) {
            $payments->markFailed($payment, $request->input('reason', 'Payment failed.'));
        }
        return response()->json(['ok' => true]);
    }

    /**
     * Receipt of a paid instalment (online or paid at the center).
     */
    public function receipt($emiId)
    {
        $user = $this->student();
        $account = StudentFees::for($user)->first(function ($a) use ($emiId) {
            return $a->emis->contains(function ($r) use ($emiId) { return $r->emi->id == $emiId && $r->status === 'paid'; });
        });
        abort_unless($account, 404);
        $row = $account->emis->first(function ($r) use ($emiId) { return $r->emi->id == $emiId; });
        $registration = RegistrationForm::find($account->application->reg_id);

        $pdf = PDF::loadView('pdf.student-emi-receipt', [
            'row' => $row,
            'account' => $account,
            'registration' => $registration,
            'gst' => (int) $account->application->gst_amount > 0,
        ]);
        return $pdf->stream('receipt-'.$row->receipt_no.'.pdf');
    }

    /**
     * Razorpay webhook: completes payments even if the student's browser
     * never returned. Not behind login or CSRF; the signature is checked.
     */
    public function webhook(Request $request, EmiPayments $payments)
    {
        $ok = $payments->webhook($request->getContent(), $request->header('X-Razorpay-Signature'));
        return response()->json(['ok' => $ok], $ok ? 200 : 400);
    }

    private function student()
    {
        abort_unless(Helper::isStudent(), 403);
        return Auth::guard()->user();
    }
}
