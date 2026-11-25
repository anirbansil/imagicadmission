<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module-wise results: full marks per module (the default for new exams) and,
 * on each result row, the module it belongs to, the evaluating faculty, the
 * exam date and the full marks used for that exam.
 */
class AddResultFieldsToStudentMarks extends Migration
{
    public function up()
    {
        Schema::table('module_master', function (Blueprint $table) {
            $table->unsignedSmallInteger('theory_full_marks')->nullable()->after('module_name');
            $table->unsignedSmallInteger('practical_full_marks')->nullable()->after('theory_full_marks');
        });

        Schema::table('student_marks', function (Blueprint $table) {
            $table->integer('module_id')->nullable()->after('module_name');
            $table->integer('faculty_id')->nullable()->after('module_id');
            $table->date('exam_date')->nullable()->after('faculty_id');
            $table->unsignedSmallInteger('theory_full_marks')->nullable()->after('marks');
            $table->unsignedSmallInteger('practical_full_marks')->nullable()->after('practical_marks');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->index(['stdnt_id', 'module_id']);
        });

        // Link existing rows to their module by name within the same course.
        DB::statement('UPDATE student_marks sm
            JOIN module_master mm ON mm.course_id = sm.course_name AND TRIM(mm.module_name) = TRIM(sm.module_name) AND mm.deleted_at IS NULL
            SET sm.module_id = mm.id
            WHERE sm.module_id IS NULL');
    }

    public function down()
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropIndex(['stdnt_id', 'module_id']);
            $table->dropColumn(['module_id', 'faculty_id', 'exam_date', 'theory_full_marks', 'practical_full_marks', 'updated_by']);
        });
        Schema::table('module_master', function (Blueprint $table) {
            $table->dropColumn(['theory_full_marks', 'practical_full_marks']);
        });
    }
}
