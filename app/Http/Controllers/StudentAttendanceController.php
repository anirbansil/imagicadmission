<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\AttendanceRequest;
use App\Models\CheckinCheckout;
use App\Services\Notifier;
use App\Services\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Student self attendance. The page loads once; everything after that
 * (check-in/out, month and page changes, requests) goes through AJAX and
 * each response carries a fresh copy of the page data.
 */
class StudentAttendanceController extends Controller {
	/**
	 * The attendance page shell with its first set of data.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (!Helper::isStudent()) {
			return redirect()->route('dashboard');
		}

		$title = 'My Attendance';
		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		$data = $this->payload($attendance, $request);

		return view('student-attendance.index', compact('title', 'user', 'data'));
	}

	/**
	 * Page data as JSON (month navigation, history pages, refresh).
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function data(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}
		return response()->json($this->payload(new StudentAttendance(Auth::guard()->user()), $request));
	}

	/**
	 * Check in for today's class.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function checkIn(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		$now = $attendance->now;

		if (CheckinCheckout::where(['user_id' => $user->id, 'date' => $now->toDateString()])->exists()) {
			return $this->fail('You have already checked in today.', $attendance, $request);
		}
		[$allowed, $message] = $attendance->checkInRule();
		if (!$allowed) {
			return $this->fail($message, $attendance, $request);
		}
		// Must be at the institute, unless an online class was approved for today.
		[$here, $message, $location] = $attendance->checkLocation($request->lat, $request->lng, $request->accuracy);
		if (!$here) {
			return $this->fail($message, $attendance, $request);
		}

		CheckinCheckout::create([
			'user_id' => $user->id,
			'course' => $attendance->courseId() ?: 0,
			'module' => '',
			'date' => $now->toDateString(),
			'check_in_time' => $now->format('H:i:s'),
			'mode' => $location['mode'],
			'check_in_lat' => $location['lat'],
			'check_in_lng' => $location['lng'],
			'check_in_accuracy' => $location['accuracy'],
			'check_in_distance' => $location['distance'],
		]);

		$slot = $attendance->classOn($now->copy()->startOfDay());
		$window = $slot ? $attendance->windows($slot) : null;
		$message = 'Checked in at '.$now->format('H:i').($location['mode'] == 'online' ? ' (online class)' : '').'.'
			.($window ? ' Check out between '.$window['out_opens'].' and '.$window['out_closes'].'.' : '');

		return $this->ok($message, $user, $request);
	}

	/**
	 * Check out of today's open check-in.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function checkOut(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		$now = $attendance->now;

		$record = CheckinCheckout::where(['user_id' => $user->id, 'date' => $now->toDateString()])->orderBy('id', 'DESC')->first();
		if (!$record) {
			return $this->fail('You have not checked in today.', $attendance, $request);
		}
		if ($record->checkout_time != null) {
			return $this->fail('You have already checked out today.', $attendance, $request);
		}
		[$allowed, $message] = $attendance->checkOutRule();
		if (!$allowed) {
			return $this->fail($message, $attendance, $request);
		}
		// Check-out works from anywhere: only the check-in is location-checked.
		$record->update([
			'checkout_time' => $now->format('H:i:s'),
		]);

		$slot = $attendance->classOn($now->copy()->startOfDay());
		$early = $slot ? $attendance->minutesEarly($now->format('H:i:s'), $slot) : 0;
		return $this->ok('Checked out at '.$now->format('H:i').'.'.($early ? ' You left '.$early.' min before class ended.' : ''), $user, $request);
	}

	/**
	 * Ask the center to correct a day: wrong times, a forgotten check-out, or a missed check-in.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function requestFix(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		$now = $attendance->now;
		$daysBack = $attendance->setting('fix_days_back');
		$earliest = $now->copy()->subDays($daysBack)->toDateString();
		$request->validate([
			'date' => 'required|date_format:Y-m-d|after_or_equal:'.$earliest.'|before_or_equal:'.$now->toDateString(),
			'check_in_time' => 'required|date_format:H:i',
			'checkout_time' => ($request->date == $now->toDateString() ? 'nullable' : 'required').'|date_format:H:i|after:check_in_time',
			'reason' => 'required|string|max:500',
		], [
			'date.after_or_equal' => 'You can only fix days from the last '.$daysBack.' days.',
			'date.before_or_equal' => 'You cannot fix a future day.',
			'checkout_time.required' => 'Please enter your check-out time.',
			'checkout_time.after' => 'Check-out time must be after check-in time.',
		]);

		$label = Carbon::parse($request->date)->format('d M Y');
		if (!$attendance->inBatch(Carbon::parse($request->date, config('attendance.timezone')))) {
			return $this->invalid('date', $label.' is outside your course period.');
		}
		$exists = AttendanceRequest::where(['user_id' => $user->id, 'type' => 'fix', 'from_date' => $request->date, 'status' => 'pending'])->exists();
		if ($exists) {
			return $this->fail('You already have a pending fix request for '.$label.'.', $attendance, $request);
		}

		$sent = AttendanceRequest::create([
			'user_id' => $user->id,
			'type' => 'fix',
			'from_date' => $request->date,
			'to_date' => $request->date,
			'check_in_time' => $request->check_in_time.':00',
			'checkout_time' => $request->checkout_time ? $request->checkout_time.':00' : null,
			'reason' => $request->reason,
			'status' => 'pending',
		]);
		Notifier::requestSubmitted($sent);

		return $this->ok('Fix request for '.$label.' sent to your center.', $user, $request);
	}

	/**
	 * Apply for leave. Must cover at least one of the student's class days.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function requestLeave(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		$now = $attendance->now;
		$daysBack = $attendance->setting('fix_days_back');
		$maxDays = $attendance->setting('max_leave_days');
		$earliest = $now->copy()->subDays($daysBack)->toDateString();
		$request->validate([
			'from_date' => 'required|date_format:Y-m-d|after_or_equal:'.$earliest,
			'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
			'reason' => 'required|string|max:500',
		], [
			'from_date.after_or_equal' => 'Leave can only start within the last '.$daysBack.' days.',
			'to_date.after_or_equal' => 'The end date must be on or after the start date.',
		]);

		$from = Carbon::parse($request->from_date, config('attendance.timezone'));
		$to = Carbon::parse($request->to_date, config('attendance.timezone'));
		if ($from->diffInDays($to) + 1 > $maxDays) {
			return $this->invalid('to_date', 'A single leave request can cover at most '.$maxDays.' days.');
		}
		if (($attendance->periodEnd && $from->toDateString() > $attendance->periodEnd) || ($attendance->periodStart && $to->toDateString() < $attendance->periodStart)) {
			return $this->invalid('from_date', 'These dates are outside your course period.');
		}

		if ($attendance->hasSchedule() && $attendance->classDaysBetween($from, $to) == 0) {
			return $this->invalid('from_date', 'You have no classes between these dates, so no leave is needed.');
		}

		$overlap = AttendanceRequest::where(['user_id' => $user->id, 'type' => 'leave'])
			->whereIn('status', ['pending', 'approved'])
			->where('from_date', '<=', $to->toDateString())
			->where('to_date', '>=', $from->toDateString())
			->exists();
		if ($overlap) {
			return $this->invalid('from_date', 'You already have leave requested for some of these days.');
		}

		$sent = AttendanceRequest::create([
			'user_id' => $user->id,
			'type' => 'leave',
			'from_date' => $from->toDateString(),
			'to_date' => $to->toDateString(),
			'reason' => $request->reason,
			'status' => 'pending',
		]);
		Notifier::requestSubmitted($sent);

		$classes = $attendance->hasSchedule() ? $attendance->classDaysBetween($from, $to) : null;
		$message = 'Leave request sent to your center'.($classes ? ' ('.$classes.' '.\Illuminate\Support\Str::plural('class', $classes).')' : '').'.';

		return $this->ok($message, $user, $request);
	}

	/**
	 * Ask to attend today's class online. Once approved, check-in and check-out
	 * work from anywhere for that day. Only on class days, and only until
	 * `online_request_cutoff` minutes before the class starts.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function requestOnline(Request $request) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}
		$request->validate(['reason' => 'required|string|max:500'], ['reason.required' => 'Please tell your center why you are attending online.']);

		$user = Auth::guard()->user();
		$attendance = new StudentAttendance($user);
		[$allowed, $message] = $attendance->onlineRequestRule();
		if (!$allowed) {
			if (!$message) {
				$message = $attendance->centerLocation()
					? 'You can ask for an online class only on your class days.'
					: 'No request is needed: your center does not check location, so you can check in from anywhere.';
			}
			return $this->fail($message, $attendance, $request);
		}

		$today = $attendance->now->copy()->startOfDay();
		$slot = $attendance->classOn($today);
		$sent = AttendanceRequest::create([
			'user_id' => $user->id,
			'type' => 'online',
			'from_date' => $today->toDateString(),
			'to_date' => $today->toDateString(),
			// The class time, so the center sees which class it is for.
			'check_in_time' => $slot['start'].':00',
			'checkout_time' => $slot['end'].':00',
			'reason' => $request->reason,
			'status' => 'pending',
		]);
		Notifier::requestSubmitted($sent);

		return $this->ok('Online class request sent. Once your center approves it, you can check in from anywhere today.', $user, $request);
	}

	/**
	 * Withdraw one of the student's own pending requests.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function cancelRequest(Request $request, $id) {
		if (!Helper::isStudent()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$user = Auth::guard()->user();
		$deleted = AttendanceRequest::where(['id' => $id, 'user_id' => $user->id, 'status' => 'pending'])->delete();
		if (!$deleted) {
			return $this->fail('That request can no longer be withdrawn.', new StudentAttendance($user), $request);
		}

		return $this->ok('Request withdrawn.', $user, $request);
	}

	/**
	 * Everything the page renders.
	 */
	private function payload(StudentAttendance $attendance, Request $request) {
		$now = $attendance->now;
		$tz = config('attendance.timezone');

		// Month overview (YYYY-MM), defaults to the current month.
		$month = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->month) ? $request->month : $now->format('Y-m');
		$monthStart = Carbon::createFromFormat('Y-m-d', $month.'-01', $tz)->startOfDay();
		$monthDays = $attendance->days($monthStart, $monthStart->copy()->endOfMonth());

		// History: paginated, newest first.
		$history = $attendance->history();
		$perPage = config('attendance.history_per_page');
		$lastPage = max(1, (int) ceil($history->count() / $perPage));
		$page = min($lastPage, max(1, (int) $request->page));

		$schedule = $attendance->currentSchedule();
		$requests = AttendanceRequest::where('user_id', $attendance->user->id)->orderBy('id', 'DESC')->get();
		$fixDays = $requests->where('type', 'fix')->where('status', 'pending')->pluck('from_date')->all();
		$fixFrom = max($now->copy()->subDays($attendance->setting('fix_days_back'))->toDateString(), $attendance->periodStart ?: '0000-00-00');

		return [
			'server_time' => $now->timestamp,
			'today' => $attendance->today(),
			'schedule' => [
				'days' => $schedule,
				'text' => StudentAttendance::describeSchedule($schedule),
				'batch' => [
					'start' => $attendance->periodStart,
					'end' => $attendance->periodEnd,
				],
			],
			'course' => $attendance->coursePeriod(),
			'month' => [
				'key' => $month,
				'label' => $monthStart->format('F Y'),
				'prev' => $monthStart->copy()->subMonth()->format('Y-m'),
				'next' => $monthStart->copy()->addMonth()->format('Y-m') <= $now->format('Y-m') ? $monthStart->copy()->addMonth()->format('Y-m') : null,
				'offset' => $monthStart->dayOfWeekIso - 1,
				'days' => $monthDays->map(function ($d) use ($now) {
					return ['date' => $d['date'], 'day' => (int) substr($d['date'], 8), 'status' => $d['status'], 'late' => $d['late'] || $d['early'],
						'class' => $d['class'], 'holiday' => $d['holiday'], 'in' => $d['in'], 'out' => $d['out'], 'today' => $d['date'] == $now->toDateString()];
				})->values(),
				'summary' => $attendance->summarize($monthDays->where('date', '<=', $now->toDateString())),
			],
			'history' => [
				'rows' => $history->forPage($page, $perPage)->map(function ($d) use ($fixDays, $fixFrom, $now) {
					$d['hours'] = $d['out'] ? StudentAttendance::formatDuration($d['seconds']) : null;
					$d['fix_pending'] = in_array($d['date'], $fixDays);
					$d['can_fix'] = in_array($d['status'], ['present', 'absent']) && $d['date'] >= $fixFrom && $d['date'] <= $now->toDateString();
					return $d;
				})->values(),
				'page' => $page,
				'last_page' => $lastPage,
				'total' => $history->count(),
				'from' => $history->isEmpty() ? 0 : ($page - 1) * $perPage + 1,
				'to' => min($page * $perPage, $history->count()),
				'summary' => $attendance->summarize($history->where('date', '<=', $now->toDateString())),
			],
			'pending' => $requests->where('status', 'pending')->map(function ($r) {
				return [
					'id' => $r->id,
					'type' => $r->type,
					'from' => Carbon::parse($r->from_date)->format('d M Y'),
					'to' => $r->to_date != $r->from_date ? Carbon::parse($r->to_date)->format('d M Y') : null,
					'times' => $r->type == 'fix' ? substr($r->check_in_time, 0, 5).' – '.($r->checkout_time ? substr($r->checkout_time, 0, 5) : '…')
						: ($r->type == 'online' && $r->check_in_time ? 'class '.substr($r->check_in_time, 0, 5).'–'.substr($r->checkout_time, 0, 5) : null),
				];
			})->values(),
			'reviewed' => $requests->whereIn('status', ['approved', 'rejected'])->take(5)->map(function ($r) {
				return [
					'type' => $r->type,
					'status' => $r->status,
					'from' => Carbon::parse($r->from_date)->format('d M Y'),
					'to' => $r->to_date != $r->from_date ? Carbon::parse($r->to_date)->format('d M Y') : null,
					'note' => $r->admin_note,
				];
			})->values(),
			'limits' => [
				'fix_from' => $fixFrom,
				'today' => $now->toDateString(),
				'fix_days_back' => $attendance->setting('fix_days_back'),
				'max_leave_days' => $attendance->setting('max_leave_days'),
				'course_end' => $attendance->periodEnd,
			],
		];
	}

	private function ok($message, $user, Request $request) {
		return response()->json(['ok' => true, 'message' => $message, 'data' => $this->payload(new StudentAttendance($user), $request)]);
	}

	private function fail($message, StudentAttendance $attendance, Request $request) {
		return response()->json(['ok' => false, 'message' => $message, 'data' => $this->payload($attendance, $request)], 409);
	}

	private function invalid($field, $message) {
		return response()->json(['message' => $message, 'errors' => [$field => [$message]]], 422);
	}
}
