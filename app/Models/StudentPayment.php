<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One online EMI payment attempt from the Student Portal.
 */
class StudentPayment extends Model
{
    const CREATED = 'created';
    const PAID = 'paid';
    const FAILED = 'failed';
    const DUPLICATE = 'duplicate';

    protected $table = 'student_payments';

    protected $fillable = [
        'user_id', 'application_id', 'emi_id', 'emi_amount', 'late_fee', 'amount', 'currency', 'gateway',
        'order_id', 'payment_id', 'method', 'status', 'error', 'receipt_no', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function emi()
    {
        return $this->belongsTo(ApplicationFormEmi::class, 'emi_id');
    }

    public function application()
    {
        return $this->belongsTo(ApplicationForm::class, 'application_id');
    }
}
