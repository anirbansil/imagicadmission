<?php

/*
|--------------------------------------------------------------------------
| Student SMS and email notifications
|--------------------------------------------------------------------------
|
| Emails to a centre go to the centre's email (Centre Master / My Centre),
| or to its centre head accounts when the centre has none, with the admin
| addresses below in CC. Every email / SMS is recorded in notification_logs.
|
*/

return [

    // Turn emails / SMS off without touching the code (e.g. on a test copy).
    'email_enabled' => env('NOTIFY_EMAIL_ENABLED', true),
    'sms_enabled' => env('SMS_ENABLED', false),

    // Sender of every email. Falls back to MAIL_FROM_ADDRESS.
    'from_address' => env('NOTIFY_FROM_ADDRESS', env('MAIL_FROM_ADDRESS') ?: 'info@imagic.net.in'),
    'from_name' => env('NOTIFY_FROM_NAME', 'IMAGIC Institute'),

    // Admin addresses put in CC on centre emails, comma-separated.
    // Leave empty to use the email of every active superadmin account.
    'admin_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('ADMIN_NOTIFY_EMAILS', ''))))),

    // MSG91 (https://control.msg91.com) Flow API.
    'msg91' => [
        'url' => 'https://control.msg91.com/api/v5/flow',
        'authkey' => env('MSG91_AUTHKEY'),
        'country_code' => '91',
        'templates' => [
            // "Dear {#var#} , Congratulation! Welcome to IMAGIC Institute. We confirm your
            // registration. Wishing you a colorful career."
            'registration' => [
                'id' => env('MSG91_REGISTRATION_TEMPLATE', '1707161546447465934'),
                // Name of the template variable that holds the student's name, as set in
                // the MSG91 flow (the sample request uses "name").
                'var' => env('MSG91_REGISTRATION_VAR', 'name'),
            ],
        ],
    ],

    // Attendance warning: absent or late for this many classes in a row.
    'warning_streak' => 3,

];
