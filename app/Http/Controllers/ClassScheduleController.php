<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesStudentAttendance;
use App\Helpers\Helper;
use App\Models\ApplicationForm;
use App\Models\AttendanceHoliday;
use App\Models\CenterMaster;
use App\Models\RegistrationForm;
use App\Models\StudentClassSchedule;
use App\Models\User;
use App\Services\AttendanceSettings;
use App\Services\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class days of each student (used by the student self attendance page) and holidays.
 * Superadmin manages every student; center accounts manage their own students.
 */
class ClassScheduleController extends Controller {
	use ManagesStudentAttendance;

	/**
	 * Students with their current class days.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (!$this->canManageAttendance()) {
			return redirect()->route('dashboard');
		}

		$title = 'Class Schedules';
		$today = StudentAttendance::now()->toDateString();

		$students = User::whereHas('roles', function ($q) { $q->where('slug', 'student'); });
		if ($this->isCenter()) {
			$students->whereIn('id', $this->centerStudentIds(Auth::guard()->user()->branch_id));
		} elseif ($request->center) {
			$students->whereIn('id', $this->centerStudentIds($request->center));
		}
		if ($request->q) {
			$q = trim($request->q);
			$regEmails = RegistrationForm::where('roll_no', 'LIKE', '%'.$q.'%')->orWhere('mobile_no', 'LIKE', '%'.$q.'%')->pluck('email');
			$students->where(function ($w) use ($q, $regEmails) {
				$w->where('name', 'LIKE', '%'.$q.'%')->orWhere('email', 'LIKE', '%'.$q.'%')->orWhereIn('email', $regEmails);
			});
		}
		if (in_array($request->schedule, ['set', 'not_set'])) {
			$withSchedule = StudentClassSchedule::where(function ($w) use ($today) {
				$w->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
			})->distinct()->pluck('user_id');
			$request->schedule == 'set' ? $students->whereIn('id', $withSchedule) : $students->whereNotIn('id', $withSchedule);
		}
		$students = $students->orderBy('name')->paginate(20)->withQueryString();

		// Registration, application and current class days for the students on this page.
		$registrations = RegistrationForm::whereIn('email', $students->pluck('email'))->orderBy('id')->get()->keyBy('email');
		$applications = ApplicationForm::whereIn('reg_id', $registrations->pluck('id'))->orderBy('id')->get()->keyBy('reg_id');
		$schedules = StudentClassSchedule::whereIn('user_id', $students->pluck('id'))
			->where(function ($w) use ($today) { $w->whereNull('effective_to')->orWhere('effective_to', '>=', $today); })
			->orderBy('day_of_week')->get()->groupBy('user_id');

		$rows = $students->map(function ($student) use ($registrations, $applications, $schedules) {
			$reg = $registrations->get($student->email);
			$app = $reg ? $applications->get($reg->id) : null;
			$days = collect($schedules->get($student->id, []))->map(function ($s) {
				return ['day' => (int) $s->day_of_week, 'name' => StudentAttendance::DAY_NAMES[$s->day_of_week], 'start' => substr($s->start_time, 0, 5), 'end' => substr($s->end_time, 0, 5), 'from' => $s->effective_from];
			})->values();
			return [
				'id' => $student->id,
				'name' => $student->name,
				'email' => $student->email,
				'roll_no' => optional($reg)->roll_no,
				'center' => $reg ? Helper::getCenterName($reg->center_name) : null,
				'course' => $app ? Helper::getCourseName($app->course) : null,
				'batch_time' => $app ? trim($app->batch_time.' '.$app->timeunit) : null,
				'batch_start' => StudentAttendance::validDate(optional($app)->batch_start_date),
				'batch_end' => StudentAttendance::validDate(optional($app)->batch_end_date),
				'days' => $days,
				'schedule_text' => StudentAttendance::describeSchedule($days),
				'starts_later' => $days->contains(function ($d) use ($days) { return $d['from'] > StudentAttendance::now()->toDateString(); }),
			];
		});

		$centers = $this->isCenter() ? collect() : CenterMaster::orderBy('name')->get(['id', 'name']);
		$holidays = $this->holidayList();
		$rules = self::editorRules(AttendanceSettings::for($this->isCenter() ? Auth::guard()->user()->branch_id : null));
		$locations = $this->locationList();

		return view('class-schedules.index', compact('title', 'students', 'rows', 'centers', 'holidays', 'request', 'today', 'rules', 'locations'));
	}

	/**
	 * One student's current class days plus a suggested class time from their batch.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id) {
		if (!$this->canManageAttendance() || !$this->manages($id)) {
			return response()->json(['message' => 'Not found'], 404);
		}

		$attendance = new StudentAttendance(User::findOrFail($id));
		$app = $attendance->application;
		$start = $app ? self::parseBatchTime($app->batch_time, $app->timeunit) : null;
		$period = $attendance->coursePeriod();

		return response()->json([
			'days' => $attendance->currentSchedule(),
			'suggested' => $start ? ['start' => $start, 'end' => Carbon::createFromFormat('H:i', $start)->addMinutes($attendance->setting('class_minutes'))->format('H:i')] : null,
			'batch_start' => $attendance->periodStart,
			'batch_end' => $attendance->periodEnd,
			'months' => $period['months'],
			'rules' => self::editorRules($attendance->settings),
		]);
	}

	/**
	 * Set class days for one or more students from a date. Earlier days keep the schedule they had.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function save(Request $request) {
		if (!$this->canManageAttendance()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$request->validate([
			'user_ids' => 'required|array|min:1|max:200',
			'user_ids.*' => 'integer',
			'effective_from' => 'required|date_format:Y-m-d',
			'days' => 'required|array',
		], [
			'user_ids.required' => 'Select at least one student.',
			'days.required' => 'Select at least one class day.',
		]);

		$days = [];
		foreach ((array) $request->days as $day => $slot) {
			if (empty($slot['on'])) {
				continue;
			}
			$day = (int) $day;
			$start = $slot['start'] ?? '';
			$end = $slot['end'] ?? '';
			if ($day < 1 || $day > 7 || !preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end)) {
				return $this->invalid('days', 'Enter a start and end time for every selected day.');
			}
			if ($end <= $start) {
				return $this->invalid('days', StudentAttendance::DAY_NAMES[$day].': the class must end after it starts.');
			}
			$days[$day] = ['start' => $start.':00', 'end' => $end.':00'];
		}
		if (!$days) {
			return $this->invalid('days', 'Select at least one class day.');
		}

		$ids = collect($request->user_ids)->map(function ($id) { return (int) $id; })->unique();
		$allowed = User::whereIn('id', $ids)->whereHas('roles', function ($q) { $q->where('slug', 'student'); })->pluck('id');
		if ($this->isCenter()) {
			$allowed = $allowed->intersect($this->centerStudentIds(Auth::guard()->user()->branch_id));
		}
		if ($allowed->count() != $ids->count()) {
			return $this->invalid('user_ids', 'Some selected students are not yours to manage.');
		}

		$from = $request->effective_from;
		$dayBefore = Carbon::parse($from)->subDay()->toDateString();
		foreach ($allowed as $userId) {
			// Replace versions that start on/after the new date, close the one running then.
			StudentClassSchedule::where('user_id', $userId)->where('effective_from', '>=', $from)->delete();
			StudentClassSchedule::where('user_id', $userId)
				->where(function ($w) use ($from) { $w->whereNull('effective_to')->orWhere('effective_to', '>=', $from); })
				->update(['effective_to' => $dayBefore]);
			foreach ($days as $day => $slot) {
				StudentClassSchedule::create([
					'user_id' => $userId,
					'day_of_week' => $day,
					'start_time' => $slot['start'],
					'end_time' => $slot['end'],
					'effective_from' => $from,
					'created_by' => Auth::guard()->user()->id,
				]);
			}
		}

		return response()->json([
			'ok' => true,
			'message' => 'Class days saved for '.$allowed->count().' '.\Illuminate\Support\Str::plural('student', $allowed->count()).' from '.Carbon::parse($from)->format('d M Y').'.',
		]);
	}

	/**
	 * Add a holiday (one date or a range). Center accounts add holidays for their own center.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addHoliday(Request $request) {
		if (!$this->canManageAttendance()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$request->validate([
			'from_date' => 'required|date_format:Y-m-d',
			'to_date' => 'nullable|date_format:Y-m-d|after_or_equal:from_date',
			'title' => 'required|string|max:191',
			'center_id' => 'nullable|integer',
		], ['to_date.after_or_equal' => 'The end date must be on or after the start date.']);

		$centerId = $this->isCenter() ? Auth::guard()->user()->branch_id : ($request->center_id ?: null);
		$from = Carbon::parse($request->from_date);
		$to = Carbon::parse($request->to_date ?: $request->from_date);
		if ($from->diffInDays($to) > 60) {
			return $this->invalid('to_date', 'A holiday range can be at most 60 days.');
		}

		$added = 0;
		for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
			$exists = AttendanceHoliday::where('date', $date->toDateString())->where(function ($q) use ($centerId) {
				$centerId ? $q->where('center_id', $centerId) : $q->whereNull('center_id');
			})->exists();
			if (!$exists) {
				AttendanceHoliday::create(['center_id' => $centerId, 'date' => $date->toDateString(), 'title' => $request->title, 'created_by' => Auth::guard()->user()->id]);
				$added++;
			}
		}

		return response()->json([
			'ok' => true,
			'message' => $added ? 'Holiday added ('.$added.' '.\Illuminate\Support\Str::plural('day', $added).').' : 'Those dates are already holidays.',
			'holidays' => $this->holidayList(),
		]);
	}

	/**
	 * Remove a holiday. Center accounts can only remove their own center's holidays.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteHoliday($id) {
		if (!$this->canManageAttendance()) {
			return response()->json(['message' => 'Unauthorized'], 403);
		}

		$holiday = AttendanceHoliday::find($id);
		if (!$holiday || ($this->isCenter() && $holiday->center_id != Auth::guard()->user()->branch_id)) {
			return response()->json(['message' => 'You cannot remove this holiday.'], 403);
		}
		$holiday->delete();

		return response()->json(['ok' => true, 'message' => 'Holiday removed.', 'holidays' => $this->holidayList()]);
	}

	/**
	 * Check-in location of each center this user manages (read only here).
	 */
	private function locationList() {
		$centers = $this->isCenter()
			? CenterMaster::where('id', Auth::guard()->user()->branch_id)->get()
			: CenterMaster::orderBy('name')->get();
		$admin = !$this->isCenter();

		return $centers->map(function ($c) use ($admin) {
			$set = is_numeric($c->latitude) && is_numeric($c->longitude);
			return [
				'id' => $c->id,
				'name' => $c->name,
				'set' => $set,
				'radius' => (int) ($c->geo_radius ?: config('attendance.defaults.geo_radius')),
				'map' => $set ? 'https://www.google.com/maps?q='.(float) $c->latitude.','.(float) $c->longitude : null,
				// Set on the centre's profile: Centre Master (admin) or My Centre (centre head).
				'edit' => $admin ? route('center-master.edit', $c->id) : route('center-master.mine'),
			];
		})->values();
	}

	/**
	 * Holidays from the last 30 days onwards that this user can see.
	 */
	private function holidayList() {
		$query = AttendanceHoliday::where('date', '>=', StudentAttendance::now()->subDays(30)->toDateString())->orderBy('date');
		if ($this->isCenter()) {
			$centerId = Auth::guard()->user()->branch_id;
			$query->where(function ($q) use ($centerId) { $q->whereNull('center_id')->orWhere('center_id', $centerId); });
		}
		$isCenter = $this->isCenter();
		$branch = Auth::guard()->user()->branch_id;

		return $query->get()->map(function ($h) use ($isCenter, $branch) {
			return [
				'id' => $h->id,
				'date' => $h->date,
				'label' => Carbon::parse($h->date)->format('D d M Y'),
				'title' => $h->title,
				'center' => $h->center_id ? Helper::getCenterName($h->center_id) : 'All centers',
				'can_delete' => !$isCenter || $h->center_id == $branch,
			];
		})->values();
	}

	private function manages($userId) {
		return !$this->isCenter() || $this->centerStudentIds(Auth::guard()->user()->branch_id)->contains((int) $userId);
	}

	/**
	 * Batch time as typed on the application ("11", "11.00", "1.15", "01:15" + AM/PM) to 24-hour HH:MM.
	 */
	/**
	 * The rules the schedule editor shows (check-in / check-out windows, usual classes a week).
	 */
	public static function editorRules($settings) {
		return array_intersect_key($settings, array_flip(['checkin_before', 'checkin_after', 'checkout_before', 'checkout_after', 'classes_per_week', 'class_minutes']));
	}

	public static function parseBatchTime($time, $unit) {
		if (!preg_match('/^\s*(\d{1,2})(?:[.:](\d{1,2}))?\s*$/', (string) $time, $m)) {
			return null;
		}
		$hour = (int) $m[1];
		$minute = isset($m[2]) ? (int) str_pad($m[2], 2, '0') : 0;
		if ($hour < 1 || $hour > 12 || $minute > 59) {
			return $hour <= 23 && $minute <= 59 ? sprintf('%02d:%02d', $hour, $minute) : null;
		}
		// "12 AM" on an application means noon (there are no midnight classes), so 12 stays 12.
		if (strtoupper(trim((string) $unit)) == 'PM' && $hour < 12) {
			$hour += 12;
		}
		return sprintf('%02d:%02d', $hour, $minute);
	}

	private function invalid($field, $message) {
		return response()->json(['message' => $message, 'errors' => [$field => [$message]]], 422);
	}
}
