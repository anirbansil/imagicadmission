<?php

namespace App\Services;

use App\Models\ApplicationForm;
use App\Models\AttendanceHoliday;
use App\Models\AttendanceRequest;
use App\Models\CenterMaster;
use App\Models\CheckinCheckout;
use App\Models\CourseMaster;
use App\Models\RegistrationForm;
use App\Models\StudentClassSchedule;
use Carbon\Carbon;

/**
 * Attendance of one student, worked out from their own class days, their
 * course period (batch start to batch end) and the attendance rules of their
 * center / course (see AttendanceSettings).
 *
 * Day statuses:
 *   present        checked in (late / early flags measured against the entry / exit windows)
 *   absent         a class day whose check-in window has passed without a check-in
 *   leave          a class day covered by approved leave
 *   leave_pending  a class day covered by leave that is waiting for approval
 *   holiday        a class day that falls on a holiday
 *   today          today's check-in window has not closed yet
 *   upcoming       a future class day
 *   no_class       not one of the student's class days, or outside the course period
 */
class StudentAttendance
{
    const DAY_NAMES = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

    public $user;
    public $now;
    public $registration;
    public $application;
    public $course;
    public $settings;
    public $periodStart;
    public $periodEnd;

    // The student's centre (CenterMaster), loaded on first use; false = not loaded yet.
    public $center = false;

    protected $schedules;
    protected $records;
    protected $requests;
    protected $holidays;

    public function __construct($user)
    {
        $this->user = $user;
        $this->now = self::now();

        // Students are linked to their registration by email (same as the profile page).
        $this->registration = RegistrationForm::where('email', $user->email)->latest()->first();
        $this->application = !empty($this->registration->id)
            ? ApplicationForm::where('reg_id', $this->registration->id)->latest()->first()
            : null;
        $this->course = !empty($this->application->course) ? CourseMaster::withTrashed()->find($this->application->course) : null;

        $centerId = optional($this->registration)->center_name;
        $this->settings = AttendanceSettings::for($centerId, optional($this->application)->course);

        // Course period: batch dates from the application; a missing end date
        // is worked out from the course duration (in days, as on the application form).
        $this->periodStart = self::validDate(optional($this->application)->batch_start_date);
        $this->periodEnd = self::validDate(optional($this->application)->batch_end_date);
        if (!$this->periodEnd && $this->periodStart && optional($this->course)->duration > 0) {
            $this->periodEnd = Carbon::parse($this->periodStart)->addDays($this->course->duration)->toDateString();
        }

        $this->schedules = StudentClassSchedule::where('user_id', $user->id)->orderBy('effective_from')->get();
        $this->requests = AttendanceRequest::where('user_id', $user->id)->orderBy('id', 'DESC')->get();

        // Latest record of each day (legacy data can hold more than one).
        $this->records = CheckinCheckout::where('user_id', $user->id)->orderBy('id')->get()
            ->keyBy(function ($r) { return Carbon::parse($r->date)->toDateString(); });

        $this->holidays = AttendanceHoliday::where(function ($q) use ($centerId) {
            $q->whereNull('center_id');
            if ($centerId) {
                $q->orWhere('center_id', $centerId);
            }
        })->get()->keyBy(function ($h) { return Carbon::parse($h->date)->toDateString(); });
    }

    public static function now()
    {
        return Carbon::now(config('attendance.timezone'));
    }

    public function setting($key)
    {
        return $this->settings[$key];
    }

    /**
     * Whether staff have set class days for this student at all.
     */
    public function hasSchedule()
    {
        return $this->schedules->isNotEmpty();
    }

    /**
     * Class time on a weekday of the given date, ignoring holidays and the course period.
     */
    public function slotFor(Carbon $date)
    {
        $d = $date->toDateString();
        $row = $this->schedules->first(function ($s) use ($date, $d) {
            return $s->day_of_week == $date->dayOfWeekIso
                && $s->effective_from <= $d
                && ($s->effective_to === null || $s->effective_to >= $d);
        });
        return $row ? ['start' => substr($row->start_time, 0, 5), 'end' => substr($row->end_time, 0, 5)] : null;
    }

    /**
     * Check-in and check-out windows of a class, as HH:MM.
     * Example (15 / 15 / 0 / 60): class 14:00–15:30 gives in 13:45–14:15, out 15:30–16:30.
     */
    public function windows($slot)
    {
        $start = self::toMinutes($slot['start']);
        $end = self::toMinutes($slot['end']);
        return [
            'in_opens' => self::fromMinutes($start - $this->settings['checkin_before']),
            'in_closes' => self::fromMinutes($start + $this->settings['checkin_after']),
            'out_opens' => self::fromMinutes($end - $this->settings['checkout_before']),
            'out_closes' => self::fromMinutes($end + $this->settings['checkout_after']),
        ];
    }

    /**
     * The student's class days that are currently in effect.
     */
    public function currentSchedule()
    {
        $today = $this->now->toDateString();
        return $this->schedules
            ->filter(function ($s) use ($today) { return $s->effective_to === null || $s->effective_to >= $today; })
            ->sortBy(function ($s) { return $s->effective_from.'-'.$s->day_of_week; })
            ->map(function ($s) {
                return [
                    'day' => (int) $s->day_of_week,
                    'name' => self::DAY_NAMES[$s->day_of_week],
                    'start' => substr($s->start_time, 0, 5),
                    'end' => substr($s->end_time, 0, 5),
                    'from' => $s->effective_from,
                ];
            })->values();
    }

    /**
     * Whether a date falls within the course period (open-ended when a date is unknown).
     */
    public function inBatch(Carbon $date)
    {
        $d = $date->toDateString();
        return (!$this->periodStart || $d >= $this->periodStart) && (!$this->periodEnd || $d <= $this->periodEnd);
    }

    public function holidayOn(Carbon $date)
    {
        return optional($this->holidays->get($date->toDateString()))->title;
    }

    /**
     * Class time if the student has a class on this date (scheduled, within the course period, not a holiday).
     */
    public function classOn(Carbon $date)
    {
        if (!$this->inBatch($date) || $this->holidayOn($date) !== null) {
            return null;
        }
        return $this->slotFor($date);
    }

    public function leaveOn(Carbon $date)
    {
        $d = $date->toDateString();
        return $this->requests->first(function ($r) use ($d) {
            return $r->type == 'leave' && in_array($r->status, ['approved', 'pending'])
                && $r->from_date <= $d && $r->to_date >= $d;
        });
    }

    /**
     * Everything about one day.
     */
    public function day(Carbon $date)
    {
        $d = $date->toDateString();
        $today = $this->now->toDateString();
        $nowMinutes = self::toMinutes($this->now->format('H:i'));
        $scheduled = $this->inBatch($date) ? $this->slotFor($date) : null;
        $holiday = $this->holidayOn($date);
        $slot = $holiday === null ? $scheduled : null;
        $windows = $slot ? $this->windows($slot) : null;
        $record = $this->records->get($d);

        $row = [
            'date' => $d,
            'label' => $date->format('d M Y'),
            'weekday' => $date->format('D'),
            'class' => $slot,
            'holiday' => $scheduled ? $holiday : null,
            'in' => null, 'out' => null, 'seconds' => 0,
            'late' => 0, 'early' => 0, 'missed_checkout' => false, 'extra' => false,
            'status' => 'no_class',
        ];

        if ($record) {
            $row['status'] = 'present';
            $row['in'] = substr($record->check_in_time, 0, 5);
            $row['out'] = $record->checkout_time ? substr($record->checkout_time, 0, 5) : null;
            $row['seconds'] = $record->checkout_time ? max(0, strtotime($record->checkout_time) - strtotime($record->check_in_time)) : 0;
            $row['missed_checkout'] = !$record->checkout_time
                && ($d < $today || ($d == $today && $windows && $nowMinutes > self::toMinutes($windows['out_closes'])));
            $row['extra'] = !$slot && $this->hasSchedule();
            if ($slot) {
                $row['late'] = $this->minutesLate($record->check_in_time, $slot);
                $row['early'] = $record->checkout_time ? $this->minutesEarly($record->checkout_time, $slot) : 0;
            }
            return $row;
        }

        if ($scheduled && $holiday !== null) {
            $row['status'] = 'holiday';
            return $row;
        }
        if (!$slot) {
            return $row;
        }

        $leave = $this->leaveOn($date);
        if ($leave) {
            $row['status'] = $leave->status == 'approved' ? 'leave' : 'leave_pending';
        } elseif ($d < $today || ($d == $today && $nowMinutes > self::toMinutes($windows['in_closes']))) {
            $row['status'] = 'absent';
        } else {
            $row['status'] = $d == $today ? 'today' : 'upcoming';
        }
        return $row;
    }

    /**
     * Days between two dates (inclusive), oldest first.
     */
    public function days(Carbon $from, Carbon $to)
    {
        $days = collect();
        for ($date = $from->copy()->startOfDay(); $date->lte($to); $date->addDay()) {
            $days->push($this->day($date->copy()));
        }
        return $days;
    }

    /**
     * Every day worth listing in the history, newest first: attended days, missed classes,
     * leave (including leave requested for coming days) and class days that fell on a holiday.
     */
    public function history()
    {
        $starts = array_filter([
            $this->records->keys()->sort()->first(),
            optional($this->schedules->first())->effective_from,
            $this->requests->where('type', 'leave')->min('from_date'),
        ]);
        if (!$starts) {
            return collect();
        }
        $from = Carbon::parse(min($starts), config('attendance.timezone'));
        if ($from->lt($this->now->copy()->subYears(3))) {
            $from = $this->now->copy()->subYears(3);
        }
        $lastLeave = $this->requests->where('type', 'leave')->whereIn('status', ['approved', 'pending'])->max('to_date');
        $to = $lastLeave && $lastLeave > $this->now->toDateString() ? Carbon::parse($lastLeave, config('attendance.timezone')) : $this->now->copy();

        return $this->days($from, $to)
            ->filter(function ($day) { return in_array($day['status'], ['present', 'absent', 'leave', 'leave_pending', 'holiday']); })
            ->reverse()->values();
    }

    /**
     * Counts for a list of days. Attendance % = classes attended / classes held.
     */
    public function summarize($days)
    {
        $present = $days->where('status', 'present');
        $attendedClasses = $present->filter(function ($d) { return $d['class'] !== null; })->count();
        $absent = $days->where('status', 'absent')->count();
        $leave = $days->where('status', 'leave')->count();
        $held = $attendedClasses + $absent + ($this->settings['leave_counts_as_absent'] ? $leave : 0);
        $seconds = $present->sum('seconds');

        return [
            'present' => $present->count(),
            'absent' => $absent,
            'leave' => $leave,
            'late_or_early' => $present->filter(function ($d) { return $d['late'] || $d['early']; })->count(),
            'held' => $held,
            'percent' => $held ? (int) round($attendedClasses * 100 / $held) : null,
            'hours' => self::formatDuration($seconds),
        ];
    }

    /**
     * Course period and how far along it the student is.
     */
    public function coursePeriod()
    {
        $start = $this->periodStart;
        $end = $this->periodEnd;
        $today = $this->now->toDateString();
        $info = [
            'name' => optional($this->course)->name,
            'start' => $start,
            'end' => $end,
            'months' => $start && $end ? max(1, (int) round((Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1) / 30.44)) : null,
            'state' => !$start ? null : ($today < $start ? 'not_started' : ($end && $today > $end ? 'ended' : 'running')),
            'progress' => null,
            'classes_total' => null,
            'classes_done' => null,
        ];
        if ($start && $end) {
            $span = max(1, Carbon::parse($start)->diffInDays(Carbon::parse($end)));
            $info['progress'] = (int) min(100, max(0, round(Carbon::parse($start)->diffInDays($this->now->copy()->startOfDay(), false) * 100 / $span)));
        }
        if ($start && $end && $this->hasSchedule()) {
            $total = $done = 0;
            for ($date = Carbon::parse($start, config('attendance.timezone')); $date->toDateString() <= $end; $date->addDay()) {
                if ($this->classOn($date)) {
                    $total++;
                    if ($date->toDateString() < $today) {
                        $done++;
                    }
                }
            }
            $info['classes_total'] = $total;
            $info['classes_done'] = $done;
        }
        return $info;
    }

    /**
     * What the today card needs, and whether check-in / check-out are allowed right now.
     */
    public function today()
    {
        $today = $this->now->copy()->startOfDay();
        $day = $this->day($today);
        $record = $this->records->get($today->toDateString());
        $slot = $day['class'];

        $state = [
            'date' => $today->toDateString(),
            'label' => $this->now->format('l j F'),
            'class' => $slot,
            'holiday' => $this->holidayOn($today),
            'on_leave' => $day['status'] == 'leave',
            'in' => $day['in'],
            'out' => $day['out'],
            'seconds' => $day['seconds'],
            'running_since' => $record && !$record->checkout_time ? Carbon::parse($today->toDateString().' '.$record->check_in_time, config('attendance.timezone'))->timestamp : null,
            'late' => $day['late'],
            'window' => $slot ? $this->windows($slot) : null,
            'early_checkout' => $this->settings['allow_early_checkout'],
            'can_check_in' => false,
            'can_check_out' => false,
            'message' => null,
            'next_class' => null,
            'has_schedule' => $this->hasSchedule(),
            'mode' => optional($record)->mode,
        ];

        // Location check and online class request for today.
        $center = $this->centerLocation();
        $online = $this->onlineRequestOn($today);
        [$canRequest, $requestMessage, $closes] = $this->onlineRequestRule();
        $leave = $this->leaveOn($today);
        $state['location'] = [
            'required' => $this->locationRequired(),
            'radius' => $center ? $center['radius'] : null,
            'center' => $center ? trim($center['name'].($center['address'] ? ', '.$center['address'] : '')) : null,
        ];
        $state['online'] = $online ? ['id' => $online->id, 'status' => $online->status, 'note' => $online->admin_note] : null;
        $state['online_request'] = [
            // Shown on class days at centers with a location check, until it is approved / used.
            'visible' => $closes !== null && !$record && (!$online || $online->status == 'rejected') && !($leave && $leave->status == 'approved'),
            'allowed' => $canRequest,
            'message' => $requestMessage,
            'closes_at' => $closes,
            'cutoff' => (int) $this->settings['online_request_cutoff'],
        ];

        if ($record) {
            [$state['can_check_out'], $state['message']] = $this->checkOutRule();
            return $state;
        }

        [$state['can_check_in'], $state['message']] = $this->checkInRule();
        $state['next_class'] = $this->nextClass($day['status'] == 'today' ? 0 : 1);
        return $state;
    }

    /**
     * [allowed, message] for checking in right now (not yet checked in).
     */
    public function checkInRule()
    {
        $today = $this->now->copy()->startOfDay();
        $d = $today->toDateString();
        if ($this->periodStart && $d < $this->periodStart) {
            return [false, 'Your course starts on '.Carbon::parse($this->periodStart)->format('d M Y').'.'];
        }
        if ($this->periodEnd && $d > $this->periodEnd) {
            return [false, 'Your course ended on '.Carbon::parse($this->periodEnd)->format('d M Y').'.'];
        }
        if (!$this->hasSchedule()) {
            // Class days not set yet: allow check-in any day, as before.
            return [true, 'Your class days have not been set by your center yet.'];
        }

        $holiday = $this->holidayOn($today);
        $slot = $this->classOn($today);
        if ($holiday !== null && $this->slotFor($today)) {
            return [false, 'No class today: '.$holiday.'.'];
        }
        if (!$slot) {
            return [false, 'You have no class today.'];
        }

        $w = $this->windows($slot);
        $now = self::toMinutes($this->now->format('H:i'));
        if ($now < self::toMinutes($w['in_opens'])) {
            return [false, 'Check-in opens at '.$w['in_opens'].'.'];
        }
        if ($now > self::toMinutes($w['in_closes'])) {
            return [false, 'Check-in closed at '.$w['in_closes'].'. If you attended, use Fix a day.'];
        }
        return [true, 'Check in by '.$w['in_closes'].'.'];
    }

    /**
     * [allowed, message] for checking out right now.
     */
    public function checkOutRule()
    {
        $today = $this->now->copy()->startOfDay();
        $record = $this->records->get($today->toDateString());
        if (!$record) {
            return [false, 'You have not checked in today.'];
        }
        if ($record->checkout_time) {
            return [false, 'Attendance completed for today.'];
        }

        $slot = $this->classOn($today);
        if (!$slot) {
            // No class time to measure against (class days not set): check out any time.
            return [true, null];
        }

        $w = $this->windows($slot);
        $now = self::toMinutes($this->now->format('H:i'));
        if ($now < self::toMinutes($w['out_opens'])) {
            return $this->settings['allow_early_checkout']
                ? [true, 'Class ends at '.$slot['end'].'. Checking out now marks you as leaving early.']
                : [false, 'Check-out opens at '.$w['out_opens'].'.'];
        }
        if ($now > self::toMinutes($w['out_closes'])) {
            return [false, 'Check-out closed at '.$w['out_closes'].'. Use Fix a day to add your check-out time.'];
        }
        return [true, 'Check out by '.$w['out_closes'].'.'];
    }

    /**
     * The next class day from today (+$offset days), within 60 days.
     */
    public function nextClass($offset = 1)
    {
        if (!$this->hasSchedule()) {
            return null;
        }
        $date = $this->now->copy()->startOfDay()->addDays($offset);
        for ($i = 0; $i < 60; $i++, $date->addDay()) {
            $slot = $this->classOn($date);
            if ($slot) {
                return ['date' => $date->toDateString(), 'label' => $date->isSameDay($this->now) ? 'Today' : $date->format('D d M'), 'start' => $slot['start'], 'end' => $slot['end']];
            }
        }
        return null;
    }

    /**
     * Class days between two dates (used to reject leave that covers no class).
     */
    public function classDaysBetween(Carbon $from, Carbon $to)
    {
        $count = 0;
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            if ($this->classOn($date)) {
                $count++;
            }
        }
        return $count;
    }

    public function courseId()
    {
        return optional($this->application)->course;
    }

    // ---------------------------------------------------------------------
    // Location check and online classes
    // ---------------------------------------------------------------------

    /**
     * The check-in location of the student's centre (from its profile), or null
     * when the centre has not set one (then check-in is not location-checked).
     */
    public function centerLocation()
    {
        if ($this->center === false) {
            $centerId = optional($this->registration)->center_name;
            $this->center = $centerId ? CenterMaster::find($centerId) : null;
        }
        $lat = optional($this->center)->latitude;
        $lng = optional($this->center)->longitude;
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }
        return [
            'lat' => (float) $lat,
            'lng' => (float) $lng,
            'radius' => (int) ($this->center->geo_radius ?: $this->settings['geo_radius']),
            'max_accuracy' => (int) $this->settings['geo_max_accuracy'],
            'name' => $this->center->name,
            'address' => $this->center->address,
        ];
    }

    /**
     * The latest online class request for a date, or null.
     */
    public function onlineRequestOn(Carbon $date)
    {
        $d = $date->toDateString();
        return $this->requests->first(function ($r) use ($d) { return $r->type == 'online' && $r->from_date == $d; });
    }

    public function onlineApprovedOn(Carbon $date)
    {
        $d = $date->toDateString();
        return $this->requests->contains(function ($r) use ($d) { return $r->type == 'online' && $r->status == 'approved' && $r->from_date == $d; });
    }

    /**
     * Whether today's check-in must be made at the center (check-out works from anywhere).
     */
    public function locationRequired()
    {
        return $this->centerLocation() !== null && !$this->onlineApprovedOn($this->now->copy()->startOfDay());
    }

    /**
     * Check the position the browser sent for a check-in.
     * Returns [ok, message, fields to store: mode, lat, lng, accuracy, distance].
     */
    public function checkLocation($lat, $lng, $accuracy)
    {
        $today = $this->now->copy()->startOfDay();
        $center = $this->centerLocation();
        $known = is_numeric($lat) && is_numeric($lng) && abs($lat) <= 90 && abs($lng) <= 180 && !((float) $lat == 0 && (float) $lng == 0);
        $acc = is_numeric($accuracy) && $accuracy >= 0 ? (int) round($accuracy) : null;
        $fields = [
            'lat' => $known ? round((float) $lat, 7) : null,
            'lng' => $known ? round((float) $lng, 7) : null,
            'accuracy' => $known ? $acc : null,
            'distance' => $known && $center ? (int) round(self::distance($lat, $lng, $center['lat'], $center['lng'])) : null,
        ];

        if ($this->onlineApprovedOn($today)) {
            return [true, null, ['mode' => 'online'] + $fields];
        }
        if (!$center) {
            return [true, null, ['mode' => null] + $fields];
        }
        if (!$known) {
            return [false, 'We need your location to confirm you are at the institute. Allow location access for this site and try again.', null];
        }
        if ($acc === null || $acc > $center['max_accuracy']) {
            return [false, 'Your location is not precise enough'.($acc !== null ? ' (within '.self::formatDistance($acc).')' : '')
                .'. Turn on GPS / precise location, step near a window, and try again.', null];
        }
        if ($fields['distance'] > $center['radius']) {
            $message = 'You are about '.self::formatDistance($fields['distance']).' from the institute. You can check in only at the institute.';
            [$canRequest] = $this->onlineRequestRule();
            if ($canRequest) {
                $message .= ' Attending online today? Send an online class request.';
            }
            return [false, $message, null];
        }
        return [true, null, ['mode' => 'campus'] + $fields];
    }

    /**
     * [allowed, message, closes_at] for asking to attend today's class online.
     * closes_at is null when the request does not apply today (no class, or
     * no location check at this center); the button is then hidden.
     */
    public function onlineRequestRule()
    {
        $today = $this->now->copy()->startOfDay();
        $slot = $this->classOn($today);
        if (!$slot || !$this->centerLocation()) {
            return [false, null, null];
        }
        $cutoff = (int) $this->settings['online_request_cutoff'];
        $closes = self::fromMinutes(self::toMinutes($slot['start']) - $cutoff);
        $request = $this->onlineRequestOn($today);

        if ($request && $request->status == 'approved') {
            return [false, 'Online class approved for today. You can check in from anywhere.', $closes];
        }
        if ($request && $request->status == 'pending') {
            return [false, 'Online class request sent. Waiting for your center to approve it.', $closes];
        }
        if ($this->records->has($today->toDateString())) {
            return [false, 'You have already checked in today.', $closes];
        }
        $leave = $this->leaveOn($today);
        if ($leave && $leave->status == 'approved') {
            return [false, 'You are on leave today.', $closes];
        }
        if (self::toMinutes($this->now->format('H:i')) >= self::toMinutes($closes)) {
            return [false, 'Online class requests closed at '.$closes.' ('.$cutoff.' minutes before class).', $closes];
        }
        return [true, 'Attending online today? Request it before '.$closes.'.', $closes];
    }

    /**
     * Distance in metres between two points (haversine).
     */
    public static function distance($lat1, $lng1, $lat2, $lng2)
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return 2 * $r * asin(min(1, sqrt($a)));
    }

    /**
     * 350 m / 2.4 km
     */
    public static function formatDistance($metres)
    {
        return $metres < 1000 ? round($metres).' m' : round($metres / 1000, 1).' km';
    }

    /**
     * Minutes after class start, when the check-in came after the check-in window closed.
     */
    public function minutesLate($checkIn, $slot)
    {
        $in = self::toMinutes(substr($checkIn, 0, 5));
        $start = self::toMinutes($slot['start']);
        return $in > $start + $this->settings['checkin_after'] ? $in - $start : 0;
    }

    /**
     * Minutes before class end, when the check-out came before the check-out window opened.
     */
    public function minutesEarly($checkOut, $slot)
    {
        $out = self::toMinutes(substr($checkOut, 0, 5));
        $end = self::toMinutes($slot['end']);
        return $out < $end - $this->settings['checkout_before'] ? $end - $out : 0;
    }

    public static function toMinutes($time)
    {
        [$h, $m] = array_map('intval', explode(':', $time) + [0, 0]);
        return $h * 60 + $m;
    }

    public static function fromMinutes($minutes)
    {
        $minutes = min(23 * 60 + 59, max(0, $minutes));
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Y-m-d, or null for empty / zero dates.
     */
    public static function validDate($value)
    {
        if (!$value || strpos((string) $value, '0000-00-00') === 0 || !strtotime($value)) {
            return null;
        }
        return Carbon::parse($value)->toDateString();
    }

    /**
     * 9h 16m
     */
    public static function formatDuration($seconds)
    {
        return sprintf('%dh %02dm', intdiv($seconds, 3600), intdiv($seconds % 3600, 60));
    }

    /**
     * Mon, Wed, Fri · 11:00–13:00 (times listed per day when they differ).
     */
    public static function describeSchedule($rows)
    {
        if ($rows->isEmpty()) {
            return null;
        }
        $times = $rows->map(function ($r) { return $r['start'].'–'.$r['end']; })->unique();
        if ($times->count() == 1) {
            return $rows->pluck('name')->implode(', ').' · '.$times->first();
        }
        return $rows->map(function ($r) { return $r['name'].' '.$r['start'].'–'.$r['end']; })->implode(', ');
    }

    /**
     * Course id from a user's latest application (used when staff approve a fix).
     */
    public static function courseIdFor($user)
    {
        $registration = RegistrationForm::where('email', $user->email)->latest()->first();
        if (empty($registration->id)) {
            return null;
        }
        return optional(ApplicationForm::where('reg_id', $registration->id)->latest()->first())->course;
    }
}
