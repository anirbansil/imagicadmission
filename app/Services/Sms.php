<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * SMS through the MSG91 Flow API (DLT-approved templates).
 */
class Sms
{
    /**
     * Send one template SMS. Returns [ok, error message or null].
     *
     * @param string $template key in config('notifications.msg91.templates')
     * @param string $mobile   10-digit Indian number (a leading 0 / +91 / 91 is fine)
     * @param array  $vars     template variables, e.g. ['name' => 'Rahul Das']
     */
    public static function send($template, $mobile, array $vars = [])
    {
        $config = config('notifications.msg91');
        $templateId = $config['templates'][$template]['id'] ?? null;
        $number = self::normalize($mobile, $config['country_code']);

        if (!$templateId) {
            return [false, 'Unknown SMS template "'.$template.'".'];
        }
        if (!$number) {
            return [false, 'Not a valid mobile number: '.$mobile];
        }
        if (empty($config['authkey'])) {
            return [false, 'MSG91_AUTHKEY is not set.'];
        }

        $response = Http::withHeaders([
            'authkey' => $config['authkey'],
            'accept' => 'application/json',
        ])->timeout(15)->post($config['url'], [
            'template_id' => $templateId,
            'recipients' => [['mobiles' => $number] + $vars],
        ]);

        // MSG91 answers {"type":"success","message":"..."} or {"type":"error","message":"..."}.
        if ($response->successful() && $response->json('type') !== 'error') {
            return [true, null];
        }
        return [false, 'MSG91 '.$response->status().': '.substr($response->body(), 0, 300)];
    }

    /**
     * "098765 43210", "+91 98765-43210" -> "919876543210"; null when it is not a mobile number.
     */
    public static function normalize($mobile, $countryCode = '91')
    {
        $digits = preg_replace('/\D+/', '', (string) $mobile);
        if (strlen($digits) == 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) == 12 && strpos($digits, $countryCode) === 0) {
            $digits = substr($digits, 2);
        }
        return preg_match('/^[6-9]\d{9}$/', $digits) ? $countryCode.$digits : null;
    }
}
