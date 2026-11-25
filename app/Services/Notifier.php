<?php

namespace App\Services;

use App\Mail\PortalMail;
use App\Models\ApplicationForm;
use App\Models\AttendanceRequest;
use App\Models\CenterMaster;
use App\Models\CourseMaster;
use App\Models\NotificationLog;
use App\Models\RegistrationForm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Student SMS and emails:
 *   registration         SMS + welcome email to the student; notification to the centre (admin in CC)
 *   leave / fix / online request
 *                        email to the centre (admin in CC); the decision email to the student
 *   attendance warning   absent or late for 3 classes in a row: alert to the centre (admin in CC),
 *                        warning to the student (see the attendance:warnings command)
 *
 * Sending never breaks the page that triggered it: during a web request it runs after the
 * response has been sent, every failure is caught, and each email / SMS is recorded in
 * notification_logs.
 */
class Notifier
{
    // ---------------------------------------------------------------------
    // Events
    // ---------------------------------------------------------------------

    /**
     * A student has been registered at a centre.
     *
     * @param int         $registrationId registration_form.id
     * @param string|null $password       the student's first login password (sent to the student only)
     */
    public static function registration($registrationId, $password = null)
    {
        self::later(function () use ($registrationId, $password) {
            $reg = RegistrationForm::find($registrationId);
            if (!$reg) {
                return;
            }
            $user = User::where('email', $reg->email)->first();
            $student = self::studentInfo($reg, $user);
            $ref = 'REG-'.$reg->id;

            // SMS: "Dear {#var#} , Congratulation! Welcome to IMAGIC Institute. ..."
            self::sms('registration', 'registration', $reg->mobile_no, [
                config('notifications.msg91.templates.registration.var') => self::smsName($reg),
            ], optional($user)->id, $ref);

            if ($reg->email) {
                self::email('registration', [$reg->email], [],
                    'Welcome to IMAGIC Institute: your registration is confirmed',
                    'registration-student',
                    ['student' => $student, 'password' => $password, 'loginUrl' => url('/')],
                    optional($user)->id, $ref);
            }

            [$to, $cc] = self::centerRecipients($reg->center_name);
            self::email('registration_center', $to, $cc,
                'New registration: '.$student['name'].' ('.$ref.')'.($student['center'] ? ', '.$student['center'] : ''),
                'registration-center',
                ['student' => $student, 'reg' => $reg, 'url' => route('registration-form.edit', $reg->id)],
                optional($user)->id, $ref);
        });
    }

    /**
     * A student has sent a leave / fix / online class request.
     */
    public static function requestSubmitted(AttendanceRequest $attendanceRequest)
    {
        $id = $attendanceRequest->id;
        self::later(function () use ($id) {
            $req = AttendanceRequest::with('user')->find($id);
            if (!$req || !$req->user) {
                return;
            }
            $reg = self::registrationOf($req->user);
            $student = self::studentInfo($reg, $req->user);
            $what = self::describeRequest($req);
            [$to, $cc] = self::centerRecipients(optional($reg)->center_name);

            self::email($req->type.'_request', $to, $cc,
                $what['title'].' request: '.$student['name'].($student['roll_no'] ? ' ('.$student['roll_no'].')' : '').' for '.$what['when_short'],
                'request-center',
                ['student' => $student, 'req' => $req, 'what' => $what,
                    'url' => route('attendance-requests.index', ['status' => 'pending', 'type' => $req->type])],
                $req->user_id, 'request#'.$req->id);
        });
    }

    /**
     * The centre / admin has approved or rejected a request: tell the student.
     */
    public static function requestReviewed(AttendanceRequest $attendanceRequest)
    {
        $id = $attendanceRequest->id;
        self::later(function () use ($id) {
            $req = AttendanceRequest::with(['user', 'reviewer'])->find($id);
            if (!$req || !$req->user || !in_array($req->status, ['approved', 'rejected'])) {
                return;
            }
            $reg = self::registrationOf($req->user);
            $student = self::studentInfo($reg, $req->user);
            $what = self::describeRequest($req);
            $approved = $req->status == 'approved';

            self::email('request_'.$req->status, [$req->user->email], [],
                $approved ? 'Approved: your '.strtolower($what['title']).' request for '.$what['when_short']
                    : 'Not approved: your '.strtolower($what['title']).' request for '.$what['when_short'],
                'request-reviewed',
                ['student' => $student, 'req' => $req, 'what' => $what, 'approved' => $approved, 'url' => route('student.attendance')],
                $req->user_id, 'request#'.$req->id);
        });
    }

    /**
     * Absent or late for several classes in a row. $days: the day rows from
     * StudentAttendance::day() that make up the streak, oldest first.
     * Sent immediately (it runs from the scheduler). Returns true when the student's email went out.
     */
    public static function attendanceWarning(User $user, $days)
    {
        $reg = self::registrationOf($user);
        $student = self::studentInfo($reg, $user);
        $count = count($days);
        $ref = end($days)['date'];
        $absent = collect($days)->where('status', 'absent')->count();
        $late = $count - $absent;
        $summary = $absent && $late ? 'absent or late' : ($absent ? 'absent' : 'late');

        [$to, $cc] = self::centerRecipients(optional($reg)->center_name);
        self::email('attendance_alert', $to, $cc,
            'Attendance alert: '.$student['name'].($student['roll_no'] ? ' ('.$student['roll_no'].')' : '').' '.$summary.' for '.$count.' classes in a row',
            'attendance-alert-center',
            ['student' => $student, 'days' => $days, 'summary' => $summary, 'count' => $count,
                'url' => route('checkin-checkout.list')],
            $user->id, $ref);

        return self::email('attendance_warning', [$user->email], [],
            'Attendance warning: '.$summary.' for '.$count.' classes in a row',
            'attendance-warning-student',
            ['student' => $student, 'days' => $days, 'summary' => $summary, 'count' => $count,
                'url' => route('student.attendance')],
            $user->id, $ref);
    }

    // ---------------------------------------------------------------------
    // Recipients
    // ---------------------------------------------------------------------

    /**
     * [to, cc] for an email to a centre: the centre's email (or its centre head
     * accounts), with the admin addresses in CC. Without a centre address it goes
     * to the admin directly.
     */
    public static function centerRecipients($centerId)
    {
        $center = self::centerEmails($centerId);
        $admin = array_values(array_diff(self::adminEmails(), $center));
        return $center ? [$center, $admin] : [$admin, []];
    }

    public static function centerEmails($centerId)
    {
        if (!$centerId) {
            return [];
        }
        $center = CenterMaster::find($centerId);
        if ($center && filter_var($center->email, FILTER_VALIDATE_EMAIL)) {
            return [strtolower($center->email)];
        }
        // No centre email yet: the centre head accounts of that branch.
        return User::where('branch_id', $centerId)->where('status', 1)->where('is_admin', '!=', 1)
            ->whereHas('roles', function ($q) { $q->where('slug', 'center'); })
            ->pluck('email')->filter(function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })
            ->map(function ($e) { return strtolower($e); })->unique()->values()->all();
    }

    public static function adminEmails()
    {
        $list = config('notifications.admin_emails');
        if (!$list) {
            $list = User::where('is_admin', 1)->where('status', 1)->pluck('email')->all();
        }
        return collect($list)->filter(function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })
            ->map(function ($e) { return strtolower(trim($e)); })->unique()->values()->all();
    }

    // ---------------------------------------------------------------------
    // Sending
    // ---------------------------------------------------------------------

    /**
     * Send one email and record it. Returns true when it was handed to the mail server.
     */
    public static function email($type, array $to, array $cc, $subject, $template, array $data, $userId = null, $ref = null)
    {
        $to = array_values(array_unique(array_filter($to, function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })));
        $cc = array_values(array_diff(array_unique(array_filter($cc, function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })), $to));
        $log = ['type' => $type, 'channel' => 'email', 'user_id' => $userId, 'ref' => $ref,
            'to' => implode(', ', $to), 'cc' => implode(', ', $cc), 'subject' => $subject];

        if (!$to) {
            return self::log($log + ['status' => 'skipped', 'error' => 'No recipient email address.']);
        }
        if (!config('notifications.email_enabled')) {
            return self::log($log + ['status' => 'skipped', 'error' => 'Emails are turned off (NOTIFY_EMAIL_ENABLED=false).']);
        }
        try {
            $mail = Mail::to($to);
            if ($cc) {
                $mail->cc($cc);
            }
            $mail->send(new PortalMail($subject, $template, $data));
            return self::log($log + ['status' => 'sent']);
        } catch (\Throwable $e) {
            Log::warning('Notification email failed', ['type' => $type, 'to' => $to, 'error' => $e->getMessage()]);
            return self::log($log + ['status' => 'failed', 'error' => substr($e->getMessage(), 0, 1000)]);
        }
    }

    /**
     * Send one template SMS and record it.
     */
    public static function sms($type, $template, $mobile, array $vars, $userId = null, $ref = null)
    {
        $log = ['type' => $type, 'channel' => 'sms', 'user_id' => $userId, 'ref' => $ref, 'to' => (string) $mobile,
            'subject' => 'Template '.$template];
        if (!config('notifications.sms_enabled')) {
            return self::log($log + ['status' => 'skipped', 'error' => 'SMS is turned off (SMS_ENABLED=false).']);
        }
        try {
            [$ok, $error] = Sms::send($template, $mobile, $vars);
            return self::log($log + ['status' => $ok ? 'sent' : 'failed', 'error' => $error]);
        } catch (\Throwable $e) {
            Log::warning('Notification SMS failed', ['type' => $type, 'mobile' => $mobile, 'error' => $e->getMessage()]);
            return self::log($log + ['status' => 'failed', 'error' => substr($e->getMessage(), 0, 1000)]);
        }
    }

    private static function log(array $row)
    {
        try {
            NotificationLog::create($row);
        } catch (\Throwable $e) {
            Log::warning('Could not record notification', $row + ['log_error' => $e->getMessage()]);
        }
        return $row['status'] === 'sent';
    }

    /**
     * Run after the web response has been sent, so a slow mail server never slows the page;
     * from the console it runs straight away.
     */
    public static function later(callable $job)
    {
        $run = function () use ($job) {
            try {
                $job();
            } catch (\Throwable $e) {
                Log::warning('Notification failed', ['error' => $e->getMessage()]);
            }
        };
        if (app()->runningInConsole()) {
            $run();
        } else {
            app()->terminating($run);
        }
    }

    // ---------------------------------------------------------------------
    // Details shown in the emails
    // ---------------------------------------------------------------------

    public static function registrationOf(User $user)
    {
        return RegistrationForm::where('email', $user->email)->latest('id')->first();
    }

    public static function studentInfo($reg, $user = null)
    {
        $name = $reg ? trim(preg_replace('/\s+/', ' ', $reg->first_name.' '.$reg->mid_name.' '.$reg->last_name)) : optional($user)->name;
        $application = $reg ? ApplicationForm::where('reg_id', $reg->id)->latest('id')->first() : null;
        $course = $application ? CourseMaster::withTrashed()->find($application->course) : null;
        $center = $reg && $reg->center_name ? CenterMaster::withTrashed()->find($reg->center_name) : null;
        return [
            'name' => $name ?: 'Student',
            'salutation' => trim(($reg->mr_mrs ?? '').' '.($reg->first_name ?? $name)),
            'roll_no' => $reg->roll_no ?? null,
            'email' => $reg->email ?? optional($user)->email,
            'mobile' => $reg->mobile_no ?? optional($user)->phone_no,
            'course' => optional($course)->name,
            'center' => optional($center)->name,
        ];
    }

    /**
     * Name for the SMS variable: DLT variables take at most 30 characters.
     */
    private static function smsName($reg)
    {
        $name = trim(preg_replace('/\s+/', ' ', $reg->mr_mrs.' '.$reg->first_name.' '.$reg->last_name));
        return mb_strlen($name) > 30 ? mb_substr(trim($reg->first_name.' '.$reg->last_name), 0, 30) : $name;
    }

    /**
     * Title, dates and times of a request, as written in the emails.
     */
    public static function describeRequest(AttendanceRequest $req)
    {
        $from = Carbon::parse($req->from_date);
        $to = Carbon::parse($req->to_date);
        $time = function ($t) { return $t ? substr($t, 0, 5) : null; };

        if ($req->type == 'leave') {
            $days = $from->diffInDays($to) + 1;
            $when = $days > 1 ? $from->format('d M Y').' to '.$to->format('d M Y').' ('.$days.' days)' : $from->format('l, d M Y');
            return ['title' => 'Leave', 'when' => $when, 'when_short' => $days > 1 ? $from->format('d M').' – '.$to->format('d M Y') : $from->format('d M Y'),
                'detail' => null, 'effect' => 'These days count as leave, not absence.'];
        }
        if ($req->type == 'online') {
            return ['title' => 'Online class', 'when' => $from->format('l, d M Y'), 'when_short' => $from->format('d M Y'),
                'detail' => 'Class '.$time($req->check_in_time).' – '.$time($req->checkout_time),
                'effect' => 'You can check in from anywhere for this class. The usual check-in and check-out times still apply.'];
        }
        return ['title' => 'Fix a day', 'when' => $from->format('l, d M Y'), 'when_short' => $from->format('d M Y'),
            'detail' => 'Check-in '.$time($req->check_in_time).($req->checkout_time ? ', check-out '.$time($req->checkout_time) : ''),
            'effect' => 'Your attendance for that day has been corrected to these times.'];
    }
}
