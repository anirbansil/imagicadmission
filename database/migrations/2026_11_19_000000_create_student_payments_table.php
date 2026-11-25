<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every online EMI payment attempt a student makes from the Student Portal.
 * The EMI itself is marked paid in application_form_emi (payment_type
 * "Online"), exactly as when a center records an online payment.
 */
class CreateStudentPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // payments are completed inside a transaction
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedInteger('application_id')->index();
            $table->unsignedInteger('emi_id')->index();
            $table->unsignedInteger('emi_amount');
            $table->unsignedInteger('late_fee')->default(0);
            $table->unsignedInteger('amount'); // total charged, in rupees
            $table->string('currency', 3)->default('INR');
            $table->string('gateway', 20)->default('razorpay');
            $table->string('order_id', 64)->unique();
            $table->string('payment_id', 64)->nullable()->index();
            $table->string('method', 30)->nullable();
            // created → paid | failed. "duplicate": paid, but the EMI had
            // already been paid another way, so it needs a refund.
            $table->string('status', 20)->default('created')->index();
            $table->string('error', 500)->nullable();
            $table->string('receipt_no', 40)->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_payments');
    }
}
