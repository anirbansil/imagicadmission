<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Student Self Attendance
    |--------------------------------------------------------------------------
    |
    | Students check in on their own class days (set per student on the
    | Class Schedules page). Times are 24-hour, India time.
    |
    */

    'timezone' => 'Asia/Kolkata',

    /*
    | Default rules. An administrator can override any of them globally, per
    | center, per course or per center + course (attendance_settings table,
    | see App\Services\AttendanceSettings). Example: class 14:00–15:30 gives
    | check-in 13:45–14:15 and check-out 15:30–16:30.
    */
    'defaults' => [
        // Check-in is open from this many minutes before the class starts ...
        'checkin_before' => 15,
        // ... until this many minutes after it starts. Later than that is late (via Fix a day only).
        'checkin_after' => 15,
        // Check-out is open from this many minutes before the class ends ...
        'checkout_before' => 0,
        // ... until this many minutes after it ends.
        'checkout_after' => 60,
        // Whether a student may check out before the check-out window opens (marked as left early).
        'allow_early_checkout' => false,

        // Suggestions for the Class Schedules editor.
        'classes_per_week' => 2,
        'class_minutes' => 90,

        // Whether approved leave lowers the attendance percentage (counted as a class not attended).
        'leave_counts_as_absent' => false,
        // How far back a student may ask to fix a day, and the longest single leave request.
        'fix_days_back' => 30,
        'max_leave_days' => 15,

        // Location check (geo-fencing) for check-in. Each centre's address and
        // latitude / longitude are set on its profile (Centre Master, or My Centre
        // for a centre head); a centre without a location does not check where
        // students are. Check-out works from anywhere.
        // Default distance when the centre has not set its own: students must be
        // within this many metres of the centre to check in ...
        'geo_radius' => 200,
        // ... and the phone / browser must know its position to at least this many metres.
        'geo_max_accuracy' => 150,
        // Online class requests close this many minutes before the class starts.
        'online_request_cutoff' => 15,
    ],

    'history_per_page' => 8,

];
