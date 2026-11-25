<?php

namespace App\Services;

use App\Models\FacultyMaster;
use App\Models\StudentMarks;
use Illuminate\Support\Collection;

/**
 * Module-wise result sheets. Marks are stored per application form (one
 * enrolment in one course) and module, with separate Theory and Practical
 * marks. Percentage and grade are worked out here, never stored.
 */
class StudentResults
{
    /**
     * Grade band for a percentage: ['grade' => 'A', 'remark' => 'Excellent', 'pass' => true].
     */
    public static function grade($percentage)
    {
        if ($percentage === null) {
            return null;
        }
        foreach (config('marks.grades') as $band) {
            if ($percentage >= $band['min']) {
                return [
                    'grade' => $band['grade'],
                    'remark' => $band['remark'],
                    'pass' => $percentage >= config('marks.pass_percentage'),
                ];
            }
        }
        return null;
    }

    public static function percentage($obtained, $full)
    {
        return $full > 0 ? round($obtained * 100 / $full, 1) : null;
    }

    /**
     * "72.5%", "80%".
     */
    public static function formatPercentage($percentage)
    {
        return $percentage === null ? '—' : rtrim(rtrim(number_format($percentage, 1), '0'), '.').'%';
    }

    /**
     * Result sheet of one enrolment.
     *
     * @param  \App\Models\ApplicationForm  $application
     * @param  \App\Models\CourseMaster  $course  with courseModule loaded
     */
    public static function sheet($application, $course)
    {
        return self::sheets(collect([$application]), collect([$course->id => $course]))[$application->id];
    }

    /**
     * Result sheets of many enrolments, keyed by application id.
     *
     * @param  Collection  $applications  ApplicationForm models
     * @param  Collection  $courses  CourseMaster models (courseModule loaded), keyed by id
     */
    public static function sheets(Collection $applications, Collection $courses)
    {
        $marks = StudentMarks::whereIn('stdnt_id', $applications->pluck('id'))->orderBy('id', 'desc')->get()->groupBy('stdnt_id');
        $faculty = FacultyMaster::withTrashed()->whereIn('id', $marks->flatten()->pluck('faculty_id')->filter()->unique())->get()->keyBy('id');

        $sheets = [];
        foreach ($applications as $application) {
            $course = $courses->get($application->course);
            $sheets[$application->id] = self::build($course, $marks->get($application->id, collect()), $faculty);
        }
        return $sheets;
    }

    /**
     * The latest result row of a module (rows saved before modules were
     * linked by id are matched on the module name).
     */
    public static function markFor($rows, $module)
    {
        return $rows->first(function ($m) use ($module) {
            return (int) $m->module_id === (int) $module->id
                || ($m->module_id === null && (string) $m->course_name === (string) $module->course_id
                    && strcasecmp(trim($m->module_name), trim($module->module_name)) === 0);
        });
    }

    private static function build($course, $rows, $faculty)
    {
        $modules = collect($course ? $course->courseModule : [])->map(function ($module) use ($rows, $faculty) {
            $mark = self::markFor($rows, $module);
            $row = (object) [
                'module' => $module,
                'mark' => $mark,
                'faculty_id' => $mark ? $mark->faculty_id : null,
                'faculty' => $mark ? StudentEnrolments::facultyName($faculty->get($mark->faculty_id)) : null,
                'exam_date' => $mark ? StudentEnrolments::date($mark->exam_date) : null,
                'theory' => $mark ? (int) $mark->marks : null,
                'practical' => $mark ? (int) $mark->practical_marks : null,
                // Full marks of that exam; older rows fall back to the module's full marks.
                'theory_full' => $mark ? ($mark->theory_full_marks ?? $module->theory_full_marks) : $module->theory_full_marks,
                'practical_full' => $mark ? ($mark->practical_full_marks ?? $module->practical_full_marks) : $module->practical_full_marks,
                'obtained' => null,
                'full' => null,
                'percentage' => null,
                'grade' => null,
            ];
            if ($mark) {
                $row->obtained = $row->theory + $row->practical;
                if ($row->theory_full !== null && $row->practical_full !== null) {
                    $row->full = (int) $row->theory_full + (int) $row->practical_full;
                    $row->percentage = self::percentage($row->obtained, $row->full);
                    $row->grade = self::grade($row->percentage);
                }
            }
            return $row;
        })->values();

        $evaluated = $modules->filter(function ($m) { return $m->percentage !== null; });
        $obtained = $evaluated->sum('obtained');
        $full = $evaluated->sum('full');
        $percentage = $evaluated->isNotEmpty() ? self::percentage($obtained, $full) : null;

        return (object) [
            'modules' => $modules,
            'total' => $modules->count(),
            'entered' => $modules->filter(function ($m) { return $m->mark !== null; })->count(),
            'evaluated' => $evaluated->count(),
            'obtained' => $obtained,
            'full' => $full,
            'percentage' => $percentage,
            'grade' => self::grade($percentage),
            // Final once every module of the course has a result.
            'final' => $modules->isNotEmpty() && $evaluated->count() === $modules->count(),
        ];
    }
}
