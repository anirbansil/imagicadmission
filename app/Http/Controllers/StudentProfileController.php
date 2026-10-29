<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\ApplicationForm;
use App\Models\RegistrationForm;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller {
	/**
	 * Read-only profile of the logged-in student.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		if (!Helper::isStudent()) {
			return redirect()->route('dashboard');
		}

		$title = 'My Profile';
		$user = Auth::guard()->user();

		// Students are linked to their registration by email (same as the dashboard).
		$registrations = RegistrationForm::where('email', $user->email)->latest()->first();
		$applications = !empty($registrations->id)
			? ApplicationForm::where('reg_id', $registrations->id)->latest()->get()
			: collect();

		return view('student-profile.index', compact('title', 'user', 'registrations', 'applications'));
	}
}
