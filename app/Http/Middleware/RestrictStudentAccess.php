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
