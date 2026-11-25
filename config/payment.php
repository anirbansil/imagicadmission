<?php

/*
|--------------------------------------------------------------------------
| Student online EMI payments (Student Portal → My Payments)
|--------------------------------------------------------------------------
*/

return [

    // Razorpay API keys (Dashboard → Account & Settings → API Keys).
    // Use rzp_test_ keys locally and rzp_live_ keys in production.
    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        // Dashboard → Webhooks, events "payment.captured" and "order.paid",
        // URL: {APP_URL}/payments/razorpay/webhook
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'currency' => 'INR',

    // Name and colour shown in the Razorpay checkout window.
    'merchant_name' => env('PAYMENT_MERCHANT_NAME', 'IMAGIC'),
    'theme_color' => '#571d1f',

    // Late fee per day after the due date, charged only on EMIs where the
    // center has switched the extra amount on (same rule as the admin side).
    'late_fee_per_day' => 50,

    // Centers whose students may pay online, as a comma-separated list of
    // center ids. Leave empty to allow every center. Online payments go to
    // the head office Razorpay account, so only enable centers whose fees
    // are collected by the head office.
    'online_centers' => array_values(array_filter(array_map('trim', explode(',', (string) env('PAYMENT_ONLINE_CENTERS', '4'))), 'strlen')),
];
