<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ApplicationForm;
use App\Models\CourseMaster;
use App\Models\FacultyMaster;
use App\Models\RegistrationForm;
use Carbon\Carbon;

/**
 * A student's course enrolments (one per application form), split into
 * current (running or upcoming) and completed (batch end date passed).
 */
class StudentEnrolments
{
    const HONORIFICS = ['mr', 'mrs', 'miss', 'ms', 'dr', 'prof'];

    /**
     * @param  \App\Models\User  $user
     * @param  array  $with  relations to eager-load on each course
     * @return object  {registrations, courses, current, completed}
     */
    public static function for($user, array $with = [])
    {
        $today = Carbon::now(config('attendance.timezone'))->startOfDay();

        // Students are linked to their registrations by email.
        $registrations = RegistrationForm::where('email', $user->email)->get()->keyBy('id');
        $applications = $registrations->isEmpty() ? collect() : ApplicationForm::whereIn('reg_id', $registrations->keys())->get();

        // Enrolled courses may since have been made inactive or deleted, so include those.
        $courses = CourseMaster::withTrashed()->with($with)
            ->whereIn('id', $applications->pluck('course')->filter()->unique())
            ->get()->keyBy('id');
        $faculty = FacultyMaster::withTrashed()->whereIn('id', $applications->pluck('faculty_id')->filter()->unique())->get()->keyBy('id');

        $enrolments = $applications->filter(function ($a) use ($courses) {
            return $courses->has($a->course);
        })->map(function ($a) use ($registrations, $courses, $faculty, $today) {
            $course = $courses->get($a->course);
            $timeline = self::timeline($a, $course, $today);
            $registration = $registrations->get($a->reg_id);
            return (object) [
                'application' => $a,
                'application_id' => $a->application_id,
                'course' => $course,
                'center' => $registration ? Helper::getCenterName($registration->center_name) : null,
                'start' => $timeline->start,
                'end' => $timeline->end,
                'batch_time' => trim($a->batch_time.' '.$a->timeunit),
                'faculty' => self::facultyName($faculty->get($a->faculty_id)),
                'state' => $timeline->state,
                'progress' => $timeline->progress,
            ];
        });

        return (object) [
            'registrations' => $registrations,
            'courses' => $courses,
            'current' => $enrolments->where('state', '!=', 'completed')->sortBy(function ($e) {
                return optional($e->start)->timestamp ?? 0;
            })->values(),
            'completed' => $enrolments->where('state', 'completed')->sortByDesc(function ($e) {
                return $e->end->timestamp;
            })->values(),
        ];
    }

    /**
     * Batch dates and state of one enrolment: {start, end, state, progress}.
     * state is running, upcoming or completed; progress is a % while running.
     */
    public static function timeline($application, $course, $today = null)
    {
        $today = $today ?: Carbon::now(config('attendance.timezone'))->startOfDay();
        $start = self::date($application->batch_start_date);
        $end = self::date($application->batch_end_date);
        if (!$end && $start && $course && (int) $course->duration > 0) {
            // No end date recorded: estimate it from the course duration (in days).
            $end = $start->copy()->addDays((int) $course->duration);
        }
        $state = $end && $end->lt($today) ? 'completed' : ($start && $start->gt($today) ? 'upcoming' : 'running');
        $progress = null;
        if ($state === 'running' && $start && $end) {
            $span = max(1, $start->diffInDays($end));
            $progress = (int) min(100, max(0, round($start->diffInDays($today) * 100 / $span)));
        }
        return (object) compact('start', 'end', 'state', 'progress');
    }

    /**
     * Display name of a faculty member. The "surname" column usually holds a
     * title (Mr, Miss…), so put a title in front of the name.
     */
    public static function facultyName($faculty)
    {
        if (!$faculty) {
            return null;
        }
        $name = trim($faculty->name);
        $surname = trim($faculty->surname);
        if ($surname === '') {
            return $name;
        }
        return in_array(strtolower(rtrim($surname, '.')), self::HONORIFICS) ? $surname.' '.$name : $name.' '.$surname;
    }

    /**
     * A date, or null when it is empty or a zero date (0000-00-00).
     */
    public static function date($value)
    {
        if (empty($value) || strpos((string) $value, '0000-00-00') === 0) {
            return null;
        }
        return Carbon::parse($value)->startOfDay();
    }
}
