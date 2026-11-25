<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\CourseMaster;
use App\Services\StudentEnrolments;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller {
	/**
	 * The logged-in student's courses (current and completed, from the
	 * application forms their center created) plus every other active
	 * course, each with its module-wise syllabus.
	 */
	public function index() {
		if (!Helper::isStudent()) {
			return redirect()->route('dashboard');
		}

		$title = 'My Courses';
		$user = Auth::guard()->user();
		$withSyllabus = ['courseModule.subModules'];

		$enrolments = StudentEnrolments::for($user, $withSyllabus);
		$registrations = $enrolments->registrations;
		$current = $enrolments->current;
		$completed = $enrolments->completed;

		$otherCourses = CourseMaster::with($withSyllabus)->where('status', 1)
			->whereNotIn('id', $enrolments->courses->keys())
			->orderBy('name')->get();

		return view('student-courses.index', compact('title', 'user', 'registrations', 'current', 'completed', 'otherCourses'));
	}

	/**
	 * Course duration (stored in days) in words, e.g. 180 → "6 months".
	 */
	public static function duration($days) {
		$days = (int) $days;
		if ($days <= 0) {
			return null;
		}
		if ($days % 365 === 0) {
			$n = $days / 365;
			return $n.' '.($n > 1 ? 'years' : 'year');
		}
		if ($days % 30 === 0) {
			$n = $days / 30;
			return $n.' '.($n > 1 ? 'months' : 'month');
		}
		return $days.' days';
	}
}
