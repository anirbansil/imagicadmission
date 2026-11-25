<?php



namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Models\InquiryForm;

use App\Models\RegistrationForm;

use App\Models\CourseMaster;
use App\Models\CourseModule;
use App\Models\CenterMaster;
use App\Models\StudentMarks;
use App\Models\CourseSubModule;
use App\Models\ApplicationForm;

use App\Models\ApplicationFormEmi;

use App\Models\FacultyMaster;
use App\Models\FacultySubject;
use App\Models\User;
use App\Models\UserDetail;
use App\Exports\StudentMarksExport;
use Maatwebsite\Excel\Facades\Excel;

use Auth;

use Illuminate\Http\Request;

use Session;

use Validator;

use DB;

use Carbon\Carbon;

use PDF;

use Helper;

class StudentsController extends Controller {



	/**



	 * Display a listing of the resource.



	 *



	 * @return \Illuminate\Http\Response



	 */



	public function index(Request $request) {



		$title = "Student's List";

		$date = date("Y-m-d");

        $students = ApplicationForm::where(function ($q) use ($request) {

            if (!empty($request->input('from_date') && !empty($request->input('to_date')))) {
                $datef = Carbon::parse($request->from_date)
                    ->startOfDay()
                    ->toDateTimeString();
                $dateto = Carbon::parse($request->to_date)
                    ->endOfDay()
                    ->toDateTimeString();
                $q->whereBetween('created_at', [$datef, $dateto]);
            }

            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
                $q->whereHas('regDetails', function ($qu) use ($request) {
                    $qu->where('center_name', $request->search_by_center);
                });
            }
            if (!empty($request->input('search_by_faculty')) ){
                $q->where('faculty_id', $request->search_by_faculty);
            }
            if(Auth::guard()->user()->roles[0]->slug == 'faculty'){
                $falculty = FacultyMaster::where('phone_no',Auth::guard()->user()->phone_no)->latest()->first();
                //dd($falculty);
                if(!empty($falculty)){
                     $q->where('faculty_id',$falculty->id);
                }
                
            }
            if(Auth::guard()->user()->roles[0]->slug == 'center'){
             $q->whereHas('regDetails', function ($qu) use ($request) {
                    $qu->where('center_name',Auth::guard()->user()->branch_id);
                });
            }
        })->where('batch_end_date','>=',$date)->orderBy('id', 'desc')->paginate(25);
        
        $centerDetails = CenterMaster::where('status',1)->get();
        $faculties = FacultyMaster::get();
        if(Auth::guard()->user()->roles[0]->slug == 'faculty'){
            $logged_in_faculty  = FacultyMaster::with('facultySubjects')->where('phone_no',Auth::user()->phone_no)->latest()->first();
        }else{
            $logged_in_faculty = "";
        }

		return view('students-list.index', compact('title', 'students', 'faculties','request','centerDetails','logged_in_faculty'));



	}

    public function ModuleByCourse(Request $request ,$id)  {
        $module = CourseModule::where('course_id',$request->id)->get(); 
        if(Auth::guard()->user()->roles[0]->slug == 'faculty'){
            $logged_in_faculty  = FacultyMaster::with('facultySubjects')->where('phone_no',Auth::user()->phone_no)->latest()->first();
            $students =ApplicationForm::with('regDetails')->where('course',$request->id)->where('faculty_id',$logged_in_faculty->id)->get();
        }else{
            $students =ApplicationForm::with('regDetails')->where('course',$request->id)->get();
        }
        $sub_module = CourseSubModule::where('course_id',$request->id)->get();
        return response()->json(['status' => 200, 'module' =>$module ,'students'=>$students,'sub_module'=>$sub_module]);
    }
    public function SubModule(Request $request ,$id)  {
        $module = CourseSubModule::where('module_id',$request->id)->get();
        return response()->json(['status' => 200, 'module' =>$module]);
    }
    public function Outsiderstudents(Request $request){
        $title ="Outsider Students";
        $users = User::whereNotNull('otp')->orderBy('id', 'DESC')->paginate(25);
        return view('students-list.outsider-student',compact('title','users'));
    }

    function StudentMarksExport(Request $request) {
        $marks_list = StudentMarks::where(function ($q) use ($request) {
            if ($request->has('course_name') && !empty($request->course_name) ){
                $q->where('course_name', $request->course_name);
            }
            if (!empty($request->input('module_name') && !empty($request->module_name)) ){
                $q->where('module_name',  'LIKE', '%' . $request->module_name . '%');
            }
            if (!empty($request->input('student')) && !empty($request->student) ){
                $q->where('stdnt_id', $request->student);
            }
            })->orderBy('id', 'desc')->get();
        
        $studentsMarks = StudentMarks::select('stdnt_id','course_name' ,DB::raw('sum(marks) as total_marks'))
        ->groupBy('stdnt_id')
        ->paginate(10)->appends(request()->query());
        $data = [
			'marks_list' => $studentsMarks
		];

		return Excel::download(new StudentMarksExport($data), 'student-marks.xlsx');
    }

    function ProgressReport(Request $request){
            $title ="Student Progress report";
            // {{-- $data = StudentMarks::where(function ($q) use ($request) {
            // if ($request->has('course_name') && !empty($request->course_name) ){
            //     $q->where('course_name', $request->course_name);
            // }
            // if (!empty($request->input('module_name') && !empty($request->module_name)) ){
            //     $q->where('module_name',  'LIKE', '%' . $request->module_name . '%');
            // }
            // if (!empty($request->input('student')) && !empty($request->student) ){
            //     $q->where('stdnt_id', $request->student);
            // }
            // })->orderBy('id', 'desc')->get(); --}}

            $studentsMarks = StudentMarks::select('stdnt_id','course_name' ,DB::raw('GROUP_CONCAT(module_name) as subjects ,GROUP_CONCAT(marks) as student_marks ,sum(marks) as total_marks'))
            ->groupBy('stdnt_id')
            ->paginate(10)->appends(request()->query());
            $courses = CourseMaster::all();
            $students =ApplicationForm::with('regDetails')->where('course',$request->course_name)->get();
            return view('reports.progress-report', compact('title','students','courses','studentsMarks'));
    }

    function getChartData(Request $request){
        $data = StudentMarks::select('stdnt_id', 'course_name', 'module_name', 'marks')
        ->orderBy('marks', 'desc')
        ->get();
        return response()->json($data);
    }

    function StudentDestory(Request $request,$id){
        try {
            $data = StudentMarks::find($id);
			$data->delete();
			return response()->json(['status' => 200, 'status_text' => 'Successfully deleted']);
		} catch (\Exception $e) {
			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);
		}
    }
}
