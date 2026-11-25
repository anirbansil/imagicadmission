<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Student attendance: leave requests and "fix a day" correction requests.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('attendance_requests')) {
            return;
        }

        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->engine = 'MyISAM'; // same engine as the other tables in this database
            $table->increments('id');
            $table->integer('user_id');
            $table->string('type', 10)->comment('leave | fix');
            $table->date('from_date');
            $table->date('to_date');
            $table->time('check_in_time')->nullable()->comment('fix only');
            $table->time('checkout_time')->nullable()->comment('fix only');
            $table->string('reason', 500);
            $table->string('status', 10)->default('pending')->comment('pending | approved | rejected');
            $table->integer('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('admin_note', 500)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'from_date'], 'attendance_requests_user_date');
            $table->index('status', 'attendance_requests_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
