<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentClassSchedulesAndHolidaysTables extends Migration
{
    /**
     * Class days per student (a student may attend 2, 3 or 4 different days a week)
     * and institute holidays, used by the student self attendance page.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('student_class_schedules')) {
            // One row per weekday the student attends. A schedule change closes the old rows
            // (effective_to) and adds new ones, so past days keep the schedule they had.
            Schema::create('student_class_schedules', function (Blueprint $table) {
                $table->engine = 'MyISAM'; // same engine as the other tables in this database
                $table->increments('id');
                $table->integer('user_id');
                $table->tinyInteger('day_of_week')->comment('1 = Monday ... 7 = Sunday');
                $table->time('start_time');
                $table->time('end_time');
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->integer('created_by')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'effective_from'], 'student_class_schedules_user');
            });
        }

        if (!Schema::hasTable('attendance_holidays')) {
            Schema::create('attendance_holidays', function (Blueprint $table) {
                $table->engine = 'MyISAM';
                $table->increments('id');
                $table->integer('center_id')->nullable()->comment('NULL = all centers');
                $table->date('date');
                $table->string('title', 191);
                $table->integer('created_by')->nullable();
                $table->timestamps();

                $table->index(['date', 'center_id'], 'attendance_holidays_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_class_schedules');
        Schema::dropIfExists('attendance_holidays');
    }
}
