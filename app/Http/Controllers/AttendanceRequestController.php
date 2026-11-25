<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesStudentAttendance;
use App\Models\AttendanceRequest;
use App\Models\CheckinCheckout;
use App\Services\Notifier;
use App\Services\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller {
	use ManagesStudentAttendance;

	/**
	 * Student leave, fix and online class requests. Center accounts only see students registered at their center.
	 * Approving an online class request lets the student check in from anywhere on that day.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (!$this->canManageAttendance()) {
			return redirect()->route('dashboard');
		}

		$title = 'Student Attendance Requests';
		$status = in_array($request->status, ['pending', 'approved', 'rejected']) ? $request->status : 'pending';

		$requests = $this->scopeToCenter(AttendanceRequest::query())
			->where('status', $status)
			->when(in_array($request->type, ['leave', 'fix', 'online']), function ($q) use ($request) {
				$q->where('type', $request->type);
			})
			->with(['user', 'reviewer'])
			->orderBy('id', $status == 'pending' ? 'ASC' : 'DESC')
			->paginate(15)->withQueryString();

		$counts = $this->scopeToCenter(AttendanceRequest::query())->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

		// Current attendance of the day, shown next to fix requests.
		$current = [];
		foreach ($requests->where('type', 'fix') as $req) {
			$current[$req->id] = CheckinCheckout::where(['user_id' => $req->user_id, 'date' => $req->from_date])->orderBy('id', 'DESC')->first();
		}

		return view('attendance-requests.index', compact('title', 'status', 'requests', 'counts', 'current', 'request'));
	}

	/**
	 * Approve a request. A fix updates (or creates) that day's attendance record.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function approve(Request $request, $id) {
		return $this->review($request, $id, 'approved');
	}

	/**
	 * Reject a request.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function reject(Request $request, $id) {
		return $this->review($request, $id, 'rejected');
	}

	private function review(Request $request, $id, $status) {
		if (!$this->canManageAttendance()) {
			return redirect()->route('dashboard');
		}
		$request->validate(['admin_note' => 'nullable|string|max:500']);

		$attendanceRequest = $this->scopeToCenter(AttendanceRequest::where('id', $id))->where('status', 'pending')->first();
		if (!$attendanceRequest) {
			return back()->with('error', 'This request was not found or has already been reviewed.');
		}

		if ($status == 'approved' && $attendanceRequest->type == 'fix') {
			$record = CheckinCheckout::where(['user_id' => $attendanceRequest->user_id, 'date' => $attendanceRequest->from_date])->orderBy('id', 'DESC')->first();
			if ($record) {
				$record->update([
					'check_in_time' => $attendanceRequest->check_in_time,
					'checkout_time' => $attendanceRequest->checkout_time,
				]);
			} else {
				CheckinCheckout::create([
					'user_id' => $attendanceRequest->user_id,
					'course' => StudentAttendance::courseIdFor($attendanceRequest->user) ?: 0,
					'module' => '',
					'date' => $attendanceRequest->from_date,
					'check_in_time' => $attendanceRequest->check_in_time,
					'checkout_time' => $attendanceRequest->checkout_time,
				]);
			}
		}

		$attendanceRequest->update([
			'status' => $status,
			'reviewed_by' => Auth::guard()->user()->id,
			'reviewed_at' => Carbon::now(),
			'admin_note' => $request->admin_note,
		]);

		// Email the decision to the student.
		Notifier::requestReviewed($attendanceRequest);

		return back()->with('msg', 'Request '.$status.'. The student has been emailed.');
	}
}
