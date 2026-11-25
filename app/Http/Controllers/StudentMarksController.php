<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Services\StudentEnrolments;
use App\Services\StudentResults;
use Illuminate\Support\Facades\Auth;

class StudentMarksController extends Controller {
	/**
	 * Module-wise results of the logged-in student's current and completed courses.
	 */
	public function index() {
		if (!Helper::isStudent()) {
			return redirect()->route('dashboard');
		}

		$title = 'My Marks';
		$user = Auth::guard()->user();
		$enrolments = StudentEnrolments::for($user, ['courseModule']);
		$all = $enrolments->current->concat($enrolments->completed);
		$sheets = StudentResults::sheets($all->pluck('application'), $enrolments->courses);

		$registrations = $enrolments->registrations;
		$current = $enrolments->current;
		$completed = $enrolments->completed;

		return view('student-marks.index', compact('title', 'user', 'registrations', 'current', 'completed', 'sheets'));
	}
}
