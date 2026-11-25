<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ApplicationForm;
use App\Models\CenterMaster;
use App\Models\CourseMaster;
use App\Models\CourseModule;
use App\Models\FacultyMaster;
use App\Models\RegistrationForm;
use App\Models\StudentMarks;
use App\Services\StudentEnrolments;
use App\Services\StudentResults;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Admin / Center / Faculty side of module results: Theory and Practical
 * marks per module, the evaluating faculty and the exam date.
 * Super Admin and Admin see every student; other staff only the students
 * registered at their own center.
 */
class ModuleMarksController extends Controller {

	/**
	 * Every enrolment with its result summary.
	 */
	public function index(Request $request) {
		$this->authorizeStaff();
		$title = 'Module Marks';

		$query = $this->applications()->with('regDetails')
			->when($request->course, function ($q) use ($request) { $q->where('course', $request->course); })
			->when($request->center && $this->seesAll(), function ($q) use ($request) {
				$q->whereHas('regDetails', function ($r) use ($request) { $r->where('center_name', $request->center); });
			})
			->when(trim((string) $request->q) !== '', function ($q) use ($request) {
				$term = '%'.trim($request->q).'%';
				$q->where(function ($w) use ($term) {
					$w->where('application_id', 'like', $term)->orWhereHas('regDetails', function ($r) use ($term) {
						$r->where(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'like', $term)
							->orWhere('roll_no', 'like', $term)->orWhere('email', 'like', $term)->orWhere('mobile_no', 'like', $term);
					});
				});
			});
		$this->filterBatch($query, $request->batch ?: 'all');

		$applications = $query->orderBy('id', 'desc')->paginate(20)->appends($request->query());
		$courses = CourseMaster::withTrashed()->with('courseModule')->whereIn('id', $applications->pluck('course')->unique())->get()->keyBy('id');
		$sheets = StudentResults::sheets($applications->getCollection(), $courses);

		$courseOptions = CourseMaster::orderBy('name')->get();
		$centers = $this->seesAll() ? CenterMaster::where('status', 1)->orderBy('name')->get() : collect();
		$seesAll = $this->seesAll();
		$courseCounts = $this->courseCounts($applications->getCollection());

		return view('module_marks_master.index', compact('title', 'request', 'applications', 'courses', 'sheets', 'courseOptions', 'centers', 'seesAll', 'courseCounts'));
	}

	/**
	 * One student with every course they are enrolled in, each with its own
	 * result summary and a link to that course's result sheet.
	 */
	public function student($regId) {
		$this->authorizeStaff();
		$registration = RegistrationForm::findOrFail($regId);
		abort_unless($this->seesAll() || $registration->center_name == Auth::guard()->user()->branch_id, 404);

		$enrolments = $this->enrolmentsOf($registration);
		$student = (object) [
			'registration' => $registration,
			'name' => trim(preg_replace('/\s+/', ' ', $registration->first_name.' '.$registration->mid_name.' '.$registration->last_name)),
			'center' => Helper::getCenterName($registration->center_name),
		];
		$title = 'Student Marks';

		return view('module_marks_master.student', compact('title', 'student', 'enrolments'));
	}

	/**
	 * Result sheet of one enrolment: every module of the course in one form.
	 */
	public function sheet($id) {
		$this->authorizeStaff();
		$application = $this->findApplication($id);
		$course = CourseMaster::withTrashed()->with('courseModule')->findOrFail($application->course);
		$sheet = StudentResults::sheet($application, $course);
		$faculties = FacultyMaster::orderBy('name')->get();
		$enrolment = $this->describe($application, $course);
		// The student's other courses, to switch between their result sheets.
		$siblings = $application->regDetails ? $this->enrolmentsOf($application->regDetails) : collect();
		$title = 'Result Sheet';

		return view('module_marks_master.sheet', compact('title', 'application', 'course', 'sheet', 'faculties', 'enrolment', 'siblings'));
	}

	public function saveSheet(Request $request, $id) {
		$this->authorizeStaff();
		$application = $this->findApplication($id);
		$course = CourseMaster::withTrashed()->with('courseModule')->findOrFail($application->course);
		$input = (array) $request->input('rows', []);

		$errors = [];
		$values = [];
		foreach ($course->courseModule as $module) {
			$parsed = $this->parseRow((array) ($input[$module->id] ?? []));
			if (is_string($parsed)) {
				$errors["rows.{$module->id}"] = $module->module_name.': '.$parsed;
			} else {
				$values[$module->id] = $parsed;
			}
		}
		if ($errors) {
			return back()->withErrors($errors)->withInput();
		}

		$existing = StudentMarks::where('stdnt_id', $application->id)->orderBy('id', 'desc')->get();
		$saved = $removed = 0;
		DB::transaction(function () use ($course, $application, $values, $existing, &$saved, &$removed) {
			foreach ($course->courseModule as $module) {
				$result = $this->store($application, $module, $values[$module->id], StudentResults::markFor($existing, $module));
				$result === 'saved' ? $saved++ : ($result === 'removed' ? $removed++ : null);
			}
		});

		Session::flash('msg', $this->summary($saved, $removed));
		return redirect()->route('student.marks.sheet', $application->id);
	}

	/**
	 * One module exam for a whole class: pick a course and module, then enter
	 * every student's Theory and Practical marks on one page.
	 */
	public function byModule(Request $request) {
		$this->authorizeStaff();
		$title = 'Enter Marks by Module';
		$courseOptions = CourseMaster::where('status', 1)->orderBy('name')->get();
		$course = $request->course ? CourseMaster::withTrashed()->with('courseModule')->find($request->course) : null;
		$module = $course ? $course->courseModule->firstWhere('id', (int) $request->module) : null;
		$batch = $request->batch ?: 'current';
		$centers = $this->seesAll() ? CenterMaster::where('status', 1)->orderBy('name')->get() : collect();
		$seesAll = $this->seesAll();

		$students = collect();
		$defaults = null;
		if ($module) {
			$query = $this->applications()->with('regDetails')->where('course', $course->id)
				->when($request->center && $this->seesAll(), function ($q) use ($request) {
					$q->whereHas('regDetails', function ($r) use ($request) { $r->where('center_name', $request->center); });
				});
			$this->filterBatch($query, $batch);
			$applications = $query->orderBy('batch_start_date', 'desc')->orderBy('id', 'desc')->get();
			$marks = StudentMarks::whereIn('stdnt_id', $applications->pluck('id'))->orderBy('id', 'desc')->get()->groupBy('stdnt_id');

			$students = $applications->map(function ($a) use ($marks, $module) {
				return (object) [
					'application' => $a,
					'mark' => StudentResults::markFor($marks->get($a->id, collect()), $module),
				];
			});

			// Pre-fill the exam details from what was used last time for this module.
			$last = $students->pluck('mark')->filter()->sortByDesc('updated_at')->first();
			$defaults = (object) [
				'theory_full' => optional($last)->theory_full_marks ?? $module->theory_full_marks ?? config('marks.default_full_marks.theory'),
				'practical_full' => optional($last)->practical_full_marks ?? $module->practical_full_marks ?? config('marks.default_full_marks.practical'),
				'faculty_id' => optional($last)->faculty_id,
				'exam_date' => optional($last)->exam_date ? StudentEnrolments::date($last->exam_date)->toDateString() : null,
			];
		}
		$faculties = FacultyMaster::orderBy('name')->get();

		return view('module_marks_master.by-module', compact('title', 'request', 'courseOptions', 'course', 'module', 'batch', 'centers', 'seesAll', 'students', 'defaults', 'faculties'));
	}

	public function saveByModule(Request $request) {
		$this->authorizeStaff();
		$course = CourseMaster::withTrashed()->with('courseModule')->findOrFail($request->course);
		$module = $course->courseModule->firstWhere('id', (int) $request->module);
		abort_unless($module, 404);

		$exam = [
			'theory_full' => $request->theory_full,
			'practical_full' => $request->practical_full,
			'faculty_id' => $request->faculty_id,
			'exam_date' => $request->exam_date,
		];
		$input = (array) $request->input('marks', []);
		$applications = $this->applications()->where('course', $course->id)->whereIn('id', array_keys($input))->get()->keyBy('id');

		$errors = [];
		$values = [];
		foreach ($input as $applicationId => $row) {
			$application = $applications->get($applicationId);
			if (!$application) {
				continue;
			}
			$parsed = $this->parseRow(array_merge($exam, (array) $row));
			if (is_string($parsed)) {
				$errors["marks.$applicationId"] = $this->studentName($application).': '.$parsed;
			} else {
				$values[$applicationId] = $parsed;
			}
		}
		if ($errors) {
			return back()->withErrors($errors)->withInput();
		}

		$existing = StudentMarks::whereIn('stdnt_id', $applications->keys())->orderBy('id', 'desc')->get()->groupBy('stdnt_id');
		$saved = $removed = 0;
		DB::transaction(function () use ($applications, $module, $values, $existing, $exam, &$saved, &$removed) {
			foreach ($values as $applicationId => $value) {
				$application = $applications->get($applicationId);
				$result = $this->store($application, $module, $value, StudentResults::markFor($existing->get($applicationId, collect()), $module));
				$result === 'saved' ? $saved++ : ($result === 'removed' ? $removed++ : null);
			}
			// Remember these full marks as the module's default for the next exam.
			if ($saved && ctype_digit((string) $exam['theory_full']) && ctype_digit((string) $exam['practical_full'])) {
				CourseModule::where('id', $module->id)->update([
					'theory_full_marks' => (int) $exam['theory_full'],
					'practical_full_marks' => (int) $exam['practical_full'],
				]);
			}
		});

		Session::flash('msg', $this->summary($saved, $removed));
		return redirect()->route('student.marks.create', $request->only('course', 'module', 'batch', 'center'));
	}

	/**
	 * Old per-row edit links open the student's result sheet.
	 */
	public function legacyEdit($id) {
		$this->authorizeStaff();
		$mark = StudentMarks::findOrFail($id);
		return redirect()->route('student.marks.sheet', $mark->stdnt_id);
	}

	// ---------------------------------------------------------------------

	/**
	 * Validate one module result. Returns null when both marks are blank
	 * (no result / remove it), the clean values, or an error message.
	 */
	private function parseRow(array $row) {
		$theory = trim((string) ($row['theory'] ?? ''));
		$practical = trim((string) ($row['practical'] ?? ''));
		if ($theory === '' && $practical === '') {
			return null;
		}

		$theoryFull = trim((string) ($row['theory_full'] ?? ''));
		$practicalFull = trim((string) ($row['practical_full'] ?? ''));
		foreach (['Theory full marks' => $theoryFull, 'Practical full marks' => $practicalFull] as $label => $value) {
			if (!ctype_digit($value) || (int) $value > 1000) {
				return "$label must be a whole number from 0 to 1000.";
			}
		}
		if ((int) $theoryFull + (int) $practicalFull === 0) {
			return 'Theory and Practical full marks cannot both be 0.';
		}

		// A section with full marks 0 is not part of this exam, so its mark may be left blank.
		$theory = $theory === '' && (int) $theoryFull === 0 ? '0' : $theory;
		$practical = $practical === '' && (int) $practicalFull === 0 ? '0' : $practical;
		foreach (['Theory' => [$theory, $theoryFull], 'Practical' => [$practical, $practicalFull]] as $label => [$value, $full]) {
			if ($value === '') {
				return "enter the $label marks too (0 if none).";
			}
			if (!ctype_digit($value)) {
				return "$label marks must be a whole number.";
			}
			if ((int) $value > (int) $full) {
				return "$label marks ($value) are more than the full marks ($full).";
			}
		}

		$facultyId = trim((string) ($row['faculty_id'] ?? ''));
		if ($facultyId !== '' && !FacultyMaster::whereKey($facultyId)->exists()) {
			return 'choose a faculty from the list.';
		}
		$examDate = trim((string) ($row['exam_date'] ?? ''));
		if ($examDate !== '') {
			try {
				$date = Carbon::createFromFormat('Y-m-d', $examDate);
			} catch (\Exception $e) {
				return 'the exam date is not a valid date.';
			}
			if ($date->format('Y-m-d') !== $examDate) {
				return 'the exam date is not a valid date.';
			}
		}

		return [
			'theory' => (int) $theory,
			'practical' => (int) $practical,
			'theory_full' => (int) $theoryFull,
			'practical_full' => (int) $practicalFull,
			'faculty_id' => $facultyId !== '' ? (int) $facultyId : null,
			'exam_date' => $examDate !== '' ? $examDate : null,
		];
	}

	/**
	 * Create, update or remove one module result. Also fills the original
	 * columns (marks, practical_marks, total_marks, module_name) that the
	 * progress report and export read.
	 */
	private function store($application, $module, $value, $existing) {
		$userId = Auth::guard()->user()->id;
		if ($value === null) {
			if ($existing) {
				$existing->update(['updated_by' => $userId]);
				$existing->delete();
				return 'removed';
			}
			return null;
		}

		$attributes = [
			'stdnt_id' => $application->id,
			'course_name' => $application->course,
			'module_name' => $module->module_name,
			'module_id' => $module->id,
			'faculty_id' => $value['faculty_id'],
			'exam_date' => $value['exam_date'],
			'marks' => $value['theory'],
			'theory_full_marks' => $value['theory_full'],
			'practical_marks' => $value['practical'],
			'practical_full_marks' => $value['practical_full'],
			'total_marks' => $value['theory'] + $value['practical'],
			'updated_by' => $userId,
		];
		if ($existing) {
			$existing->update($attributes);
		} else {
			StudentMarks::create($attributes + ['created_by' => $userId]);
		}
		return 'saved';
	}

	private function summary($saved, $removed) {
		if (!$saved && !$removed) {
			return 'Nothing to save: no marks were entered.';
		}
		$parts = [];
		if ($saved) {
			$parts[] = $saved.' '.($saved === 1 ? 'result' : 'results').' saved';
		}
		if ($removed) {
			$parts[] = $removed.' removed';
		}
		return ucfirst(implode(', ', $parts)).'.';
	}

	private function filterBatch($query, $batch) {
		$today = Carbon::now(config('attendance.timezone'))->toDateString();
		if ($batch === 'current') {
			$query->where(function ($q) use ($today) {
				$q->whereNull('batch_end_date')->orWhere('batch_end_date', '<', '1000-01-01')->orWhere('batch_end_date', '>=', $today);
			});
		} elseif ($batch === 'completed') {
			$query->where('batch_end_date', '>=', '1000-01-01')->where('batch_end_date', '<', $today);
		}
		return $query;
	}

	private function describe($application, $course) {
		$registration = $application->regDetails;
		return (object) [
			'name' => $this->studentName($application),
			'roll_no' => optional($registration)->roll_no,
			'center' => $registration ? Helper::getCenterName($registration->center_name) : null,
			'start' => StudentEnrolments::date($application->batch_start_date),
			'end' => StudentEnrolments::date($application->batch_end_date),
			'course' => $course->name,
		];
	}

	/**
	 * Registration ids of one student. A student who registered again keeps
	 * the same email, so their courses are split across registrations;
	 * group them the same way the student portal does.
	 */
	private function registrationIds($registration) {
		$email = trim((string) $registration->email);
		return $email === '' ? collect([$registration->id]) : RegistrationForm::where('email', $email)->pluck('id');
	}

	/**
	 * Every course of one student that this user may see, with its timeline
	 * and result summary. Current and upcoming courses first, then completed.
	 */
	private function enrolmentsOf($registration) {
		$applications = $this->applications()->with('regDetails')->whereIn('reg_id', $this->registrationIds($registration))->get();
		$courses = CourseMaster::withTrashed()->with('courseModule')->whereIn('id', $applications->pluck('course')->unique())->get()->keyBy('id');
		$applications = $applications->filter(function ($a) use ($courses) { return $courses->has($a->course); });
		$sheets = StudentResults::sheets($applications, $courses);
		$today = Carbon::now(config('attendance.timezone'))->startOfDay();

		return $applications->map(function ($a) use ($courses, $sheets, $today) {
			$course = $courses->get($a->course);
			$timeline = StudentEnrolments::timeline($a, $course, $today);
			return (object) [
				'application' => $a,
				'course' => $course,
				'sheet' => $sheets[$a->id],
				'start' => $timeline->start,
				'end' => $timeline->end,
				'state' => $timeline->state,
				'progress' => $timeline->progress,
				'center' => $a->regDetails ? Helper::getCenterName($a->regDetails->center_name) : null,
			];
		})->sortBy(function ($e) {
			// Running/upcoming by start date, then completed with the latest first.
			return $e->state === 'completed'
				? '1-'.(9999999999 - optional($e->end)->timestamp)
				: '0-'.str_pad((string) (optional($e->start)->timestamp ?? 0), 10, '0', STR_PAD_LEFT);
		})->values();
	}

	/**
	 * Number of visible courses per student, keyed by reg_id, for the given
	 * applications (students grouped by email as in registrationIds()).
	 */
	private function courseCounts($applications) {
		$registrations = $applications->pluck('regDetails')->filter();
		if ($registrations->isEmpty()) {
			return [];
		}
		$emails = $registrations->pluck('email')->map(function ($e) { return trim((string) $e); })->filter()->unique();
		$related = RegistrationForm::whereIn('email', $emails)->get(['id', 'email']);
		$keyOf = function ($r) { return trim((string) $r->email) !== '' ? 'e:'.strtolower(trim($r->email)) : 'r:'.$r->id; };
		$keys = $related->concat($registrations)->mapWithKeys(function ($r) use ($keyOf) { return [$r->id => $keyOf($r)]; });

		$perKey = $this->applications()->whereIn('reg_id', $keys->keys())
			->whereIn('course', CourseMaster::withTrashed()->select('id'))
			->get(['reg_id'])->countBy(function ($a) use ($keys) { return $keys[$a->reg_id]; });

		return $registrations->mapWithKeys(function ($r) use ($keyOf, $perKey) {
			return [$r->id => $perKey[$keyOf($r)] ?? 1];
		})->all();
	}

	private function studentName($application) {
		$r = $application->regDetails;
		return $r ? trim(preg_replace('/\s+/', ' ', $r->first_name.' '.$r->mid_name.' '.$r->last_name)) : 'Application '.$application->application_id;
	}

	private function authorizeStaff() {
		abort_if(Helper::isStudent() || !Helper::sidenavAccess('marks_module'), 403);
	}

	/**
	 * Super Admin and Admin see every center's students.
	 */
	private function seesAll() {
		$user = Auth::guard()->user();
		return $user->is_admin == 1 || $user->roles->pluck('slug')->intersect(['superadmin', 'admin'])->isNotEmpty();
	}

	/**
	 * Application forms this user may see.
	 */
	private function applications() {
		$query = ApplicationForm::query();
		if (!$this->seesAll()) {
			$branch = Auth::guard()->user()->branch_id;
			$query->whereHas('regDetails', function ($r) use ($branch) { $r->where('center_name', $branch ?: -1); });
		}
		return $query;
	}

	private function findApplication($id) {
		return $this->applications()->with('regDetails')->findOrFail($id);
	}
}
