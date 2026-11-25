<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * notification_logs: every email / SMS sent to students, centres and admin
 * (also stops an attendance warning from going out twice).
 * center_master.email: where a centre's notifications go.
 */
class CreateNotificationLogsAndCenterEmail extends Migration
{
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('type', 50)->index();       // registration, leave_request, request_approved, attendance_warning, ...
            $table->string('channel', 10);             // email / sms
            $table->unsignedBigInteger('user_id')->nullable()->index(); // the student it is about
            $table->string('ref', 50)->nullable();     // e.g. attendance request id, registration id, warning date
            $table->text('to')->nullable();
            $table->text('cc')->nullable();
            $table->string('subject')->nullable();
            $table->string('status', 10);              // sent / failed / skipped
            $table->text('error')->nullable();
            $table->timestamps();
        });

        Schema::table('center_master', function (Blueprint $table) {
            $table->string('email', 191)->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs');
        Schema::table('center_master', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
