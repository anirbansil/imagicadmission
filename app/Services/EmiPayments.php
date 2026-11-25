<?php

namespace App\Services;

use App\Models\ApplicationFormEmi;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

/**
 * Online EMI payments through Razorpay.
 *
 * 1. start(): the server works out the amount and creates a Razorpay order.
 * 2. The student pays in Razorpay Checkout.
 * 3. confirm() (browser callback), the webhook, or reconcile() (on the next
 *    page visit) fetch the payment from Razorpay, check it matches the
 *    order, and complete() marks the EMI paid exactly once.
 */
class EmiPayments
{
    private $api;

    private function api()
    {
        return $this->api ?: $this->api = new Api(config('payment.razorpay.key_id'), config('payment.razorpay.key_secret'));
    }

    /**
     * Create (or reuse) an order for the student's next unpaid EMI.
     *
     * @throws \RuntimeException with a message for the student
     */
    public function start($user, $emiId)
    {
        $account = StudentFees::for($user)->first(function ($a) use ($emiId) {
            return $a->emis->contains(function ($r) use ($emiId) { return $r->emi->id == $emiId; });
        });
        if (!$account) {
            throw new \RuntimeException('This instalment was not found in your account.');
        }
        $row = $account->emis->first(function ($r) use ($emiId) { return $r->emi->id == $emiId; });
        if ($row->status === 'paid') {
            throw new \RuntimeException('This instalment is already paid.');
        }
        if (!$account->online) {
            throw new \RuntimeException('Online payment is not available for your center. Please pay at your center.');
        }
        if (!$row->payable) {
            throw new \RuntimeException('Please pay your earlier instalment (EMI '.$account->next->number.') first.');
        }
        if ($row->payable_amount < 1) {
            throw new \RuntimeException('There is nothing to pay for this instalment.');
        }

        // Re-use a recent unpaid order for the same amount instead of creating another.
        $payment = StudentPayment::where('user_id', $user->id)->where('emi_id', $emiId)
            ->where('status', StudentPayment::CREATED)->where('amount', $row->payable_amount)
            ->where('created_at', '>=', now()->subHour())->latest('id')->first();
        if ($payment) {
            return $payment;
        }

        $order = $this->api()->order->create([
            'amount' => $row->payable_amount * 100,
            'currency' => config('payment.currency'),
            'receipt' => 'emi_'.$emiId.'_'.time(),
            'payment_capture' => 1,
            'notes' => [
                'emi_id' => (string) $emiId,
                'application' => (string) $account->application->application_id,
                'instalment' => (string) $row->number,
            ],
        ]);

        return StudentPayment::create([
            'user_id' => $user->id,
            'application_id' => $account->application->id,
            'emi_id' => $emiId,
            'emi_amount' => $row->amount,
            'late_fee' => $row->late_fee,
            'amount' => $row->payable_amount,
            'currency' => config('payment.currency'),
            'order_id' => $order['id'],
            'status' => StudentPayment::CREATED,
        ]);
    }

    /**
     * Browser callback after Checkout: check the signature, then confirm with Razorpay.
     */
    public function confirm(StudentPayment $payment, $paymentId, $signature)
    {
        try {
            $this->api()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $payment->order_id,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);
        } catch (SignatureVerificationError $e) {
            Log::warning('Student payment signature mismatch', ['payment' => $payment->id, 'razorpay_payment_id' => $paymentId]);
            return $payment;
        }
        return $this->apply($payment, $this->api()->payment->fetch($paymentId)->toArray());
    }

    /**
     * Check an order that was left unfinished (browser closed after paying).
     */
    public function reconcile(StudentPayment $payment)
    {
        $items = $this->api()->order->fetch($payment->order_id)->payments()->toArray()['items'] ?? [];
        foreach ($items as $item) {
            if (in_array($item['status'], ['captured', 'authorized'])) {
                return $this->apply($payment, $item);
            }
        }
        return $payment;
    }

    /**
     * Razorpay webhook (payment.captured / order.paid).
     */
    public function webhook($body, $signature)
    {
        $secret = config('payment.razorpay.webhook_secret');
        if (!$secret || !$signature) {
            return false;
        }
        try {
            $this->api()->utility->verifyWebhookSignature($body, $signature, $secret);
        } catch (SignatureVerificationError $e) {
            return false;
        }
        $event = json_decode($body, true);
        $item = $event['payload']['payment']['entity'] ?? null;
        if ($item && !empty($item['order_id'])) {
            $payment = StudentPayment::where('order_id', $item['order_id'])->first();
            if ($payment) {
                $this->apply($payment, $item);
            }
        }
        return true;
    }

    public function markFailed(StudentPayment $payment, $message)
    {
        if ($payment->status === StudentPayment::CREATED) {
            $payment->update(['error' => mb_substr((string) $message, 0, 500)]);
        }
    }

    /**
     * Act on a Razorpay payment entity that belongs to this order.
     */
    private function apply(StudentPayment $payment, array $item)
    {
        if (($item['order_id'] ?? null) !== $payment->order_id) {
            Log::warning('Razorpay payment does not belong to the order', ['payment' => $payment->id, 'item' => $item['id'] ?? null]);
            return $payment;
        }
        if ((int) $item['amount'] !== $payment->amount * 100 || strtoupper($item['currency']) !== $payment->currency) {
            Log::error('Razorpay amount mismatch', ['payment' => $payment->id, 'item' => $item['id'], 'amount' => $item['amount']]);
            $payment->update(['error' => 'Amount paid does not match the instalment.']);
            return $payment;
        }
        if ($item['status'] === 'authorized') {
            // Orders are auto-captured; capture here only if that did not happen.
            $item = $this->api()->payment->fetch($item['id'])->capture(['amount' => $item['amount'], 'currency' => $item['currency']])->toArray();
        }
        if ($item['status'] === 'captured') {
            return $this->complete($payment, $item['id'], $item['method'] ?? null);
        }
        if ($item['status'] === 'failed') {
            $this->markFailed($payment, $item['error_description'] ?? 'Payment failed.');
        }
        return $payment->fresh();
    }

    /**
     * Mark the EMI paid. Safe to call more than once for the same payment.
     */
    public function complete(StudentPayment $payment, $gatewayPaymentId, $method = null)
    {
        return DB::transaction(function () use ($payment, $gatewayPaymentId, $method) {
            $payment = StudentPayment::lockForUpdate()->findOrFail($payment->id);
            if (in_array($payment->status, [StudentPayment::PAID, StudentPayment::DUPLICATE])) {
                return $payment;
            }

            $emi = ApplicationFormEmi::lockForUpdate()->find($payment->emi_id);
            $now = now();
            if (!$emi || StudentFees::isPaid($emi)) {
                // The center recorded this instalment in the meantime: the money
                // was taken, so keep the payment for a refund instead of losing it.
                $payment->update([
                    'status' => StudentPayment::DUPLICATE,
                    'payment_id' => $gatewayPaymentId,
                    'method' => $method,
                    'paid_at' => $now,
                    'error' => 'Instalment was already paid. Refund required.',
                ]);
                Log::error('Student paid an EMI that was already paid', ['payment' => $payment->id, 'emi' => $payment->emi_id]);
                return $payment;
            }

            // Same columns the center fills when it records an online payment.
            DB::table('application_form_emi')->where('id', $emi->id)->update([
                'payment_type' => 'Online',
                'cheque_no' => $gatewayPaymentId,
                'extra_amount' => $payment->late_fee,
                'extra_amount_add' => $payment->late_fee > 0 ? 'on' : $emi->extra_amount_add,
                'updated_by' => $payment->user_id,
                'update_date' => StudentFees::today()->toDateString(),
                'updated_at' => $now,
            ]);
            $payment->update([
                'status' => StudentPayment::PAID,
                'payment_id' => $gatewayPaymentId,
                'method' => $method,
                'paid_at' => $now,
                'error' => null,
                'receipt_no' => 'ONL-'.$now->format('Ymd').'-'.str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            ]);
            return $payment;
        });
    }
}
