<?php
namespace App\Http\Middleware;
use App\Helpers\Helper;
use Closure;

class RestrictStudentAccess {
	/**
	 * Route names a Student account may open. Everything else in the
	 * logged-in area sends them back to their profile. Add routes here
	 * as student features are introduced.
	 */
	const STUDENT_ROUTES = [
		'student.profile',
		'student.courses',
		'student.my-marks',
		'student.payments',
		'student.payments.order',
		'student.payments.verify',
		'student.payments.failed',
		'student.payments.receipt',
		'student.attendance',
		'student.attendance.data',
		'student.attendance.checkin',
		'student.attendance.checkout',
		'student.attendance.fix',
		'student.attendance.leave',
		'student.attendance.online',
		'student.attendance.cancel',
		'logout',
	];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (Helper::isStudent() && !in_array($request->route()->getName(), self::STUDENT_ROUTES)) {
			if ($request->expectsJson()) {
				return response()->json(['message' => 'Unauthorized'], 403);
			}
			return redirect()->route('student.profile');
		}
		return $next($request);
	}
}
