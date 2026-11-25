<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Attendance rules an administrator can change (entry / exit windows, leave rules, ...).
 * A row with no center and no course is the global value; a row for a center, a course,
 * or both overrides it for those students. Keys not stored here use config/attendance.php.
 */
class CreateAttendanceSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('attendance_settings')) {
            return;
        }

        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('center_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('key', 50);
            $table->string('value', 191)->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['key', 'center_id', 'course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_settings');
    }
}
