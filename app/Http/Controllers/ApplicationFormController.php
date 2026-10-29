<?php



namespace App\Http\Controllers;



use App\Http\Controllers\Controller;

use App\Models\InquiryForm;

use App\Models\RegistrationForm;

use App\Models\CourseMaster;

use App\Models\CenterMaster;

use App\Models\ApplicationForm;

use App\Models\ApplicationFormEmi;

use App\Models\FacultyMaster;

use App\Models\FacultySubject;

use App\Models\User;

use Auth;

use Illuminate\Http\Request;

use Session;

use Validator;

use DB;

use Carbon\Carbon;

use PDF;

use Helper;

class ApplicationFormController extends Controller {



    /**



     * Display a listing of the resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function index(Request $request) {

        $title = 'Application';
      
        $query = ApplicationForm::query();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ROLE BASE FILTER (YOUR WORKING LOGIC)
        |--------------------------------------------------------------------------
        */

        // SUPER ADMIN (branch_id is NULL)
        if (is_null($user->branch_id)) {

            // no filter → show all data

        } else {

            // center filter
            $query->whereHas('regDetails', function ($q) use ($user) {
                $q->where('center_name', $user->branch_id);
            });

            // counselor restriction
            if ($user->hasRole('counselor')) {
                $query->where('created_by', $user->id);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_date') && $request->filled('to_date')) {

            $from = Carbon::parse($request->from_date)->startOfDay();
            $to   = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        /*
        |--------------------------------------------------------------------------
        | COURSE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search_by_course')) {

            $query->whereHas('regDetails', function ($q) use ($request) {
                $q->where('course', $request->search_by_course);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CENTER FILTER (extra manual filter if used in search form)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search_by_center')) {

            $query->whereHas('regDetails', function ($q) use ($request) {
                $q->where('center_name', $request->search_by_center);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH FILTER (FIXED SAFE GROUPING)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('input_search')) {

            $search = $request->input_search;

            $query->where(function ($q) use ($search) {

                $q->where('application_id', 'LIKE', "%{$search}%")
                  ->orWhere('reg_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('regDetails', function ($qu) use ($search) {

                      $qu->where(DB::raw('CONCAT(first_name," ",mid_name," ",last_name)'), 'LIKE', "%{$search}%")
                         ->orWhere(DB::raw('CONCAT(first_name," ",last_name)'), 'LIKE', "%{$search}%")
                         ->orWhere('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('mid_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('qualification', 'LIKE', "%{$search}%");
                  });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL RESULT
        |--------------------------------------------------------------------------
        */

        $applications = $query->orderByDesc('id')
            ->paginate(25)
            ->appends(request()->query());
      
      /*$query = ApplicationForm::query();

      $user = Auth::user();

      // SUPER ADMIN (branch_id is NULL)
      if (is_null($user->branch_id)) {

          // no filter → show all data

      } else {

          // normal users → filter by branch
          $query->whereHas('regDetails', function ($q) use ($user) {
              $q->where('center_name', $user->branch_id);
          });

          // counselor extra restriction
          if ($user->hasRole('counselor')) {
              $query->where('created_by', $user->id);
          }
      }

      $applications = $query->orderByDesc('id')
          ->paginate(25)
          ->appends(request()->query());*/


        /*$applications = ApplicationForm::where(function ($q) use ($request) {

            if (!empty($request->input('from_date') && !empty($request->input('to_date')))) {
                $datef = Carbon::parse($request->from_date)
                    ->startOfDay()
                    ->toDateTimeString();
                $dateto = Carbon::parse($request->to_date)
                    ->endOfDay()
                    ->toDateTimeString();
                $q->whereBetween('created_at', [$datef, $dateto]);
            }

            if ($request->has('search_by_course') && !empty($request->search_by_course) ){
                $q->whereHas('regDetails', function ($qu) use ($request) {
                    $qu->where('course', $request->search_by_course);
                    // $qu->where('center_name', $request->search_by_center);
                });
            }

            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
                $q->whereHas('regDetails', function ($qu) use ($request) {
                    $qu->where('center_name', $request->search_by_center);
                });
            }

            if (!empty($request->input_search)) {
                
                $q->where('application_id', 'LIKE', '%' . $request->input_search . '%');
                $q->orWhereHas('regDetails', function ($qu) use ($request) {
                    $qu->where(DB::raw('CONCAT(first_name," ",mid_name," ",last_name)'),'LIKE', '%' . $request->input_search .'%')
                    ->orWhere(DB::raw('CONCAT(first_name," ",last_name)'),'LIKE', '%' . $request->input_search .'%')
                    ->orWhere('first_name', 'LIKE', '%'.$request->input_search .'%')
                    ->orWhere('mid_name', 'LIKE', '%'.$request->input_search.'%')
                    ->orWhere('last_name', 'LIKE', '%' . $request->input_search . '%')
                    // ->orWhere('center_name', 'LIKE', '%' . $request->input_search . '%')
                    ->orWhere('qualification', 'LIKE', '%' . $request->input_search . '%');
                });
                $q->orWhere('reg_id', 'LIKE', '%' .$request->input_search.'%');
            }
            if(Auth::guard()->user()->is_admin != 1){
                $q->whereHas('regDetails', function ($qu) use ($request) {
                    //$qu->where('center_name',Auth::guard()->user()->branch_id);
					$qu->where('created_by', Auth::guard()->id());
                });
                
            }
        })->orderBy('id', 'desc')->paginate(25)->appends(request()->except('page'));*/
        // })->orderBy('id', 'desc')->toSql();

        // dd($applications);
        $centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();
        $courseDetails = CourseMaster::where('status',1)->get();

        return view('application-form.index', compact('title', 'applications', 'centerDetails', 'request', 'courseDetails'));



    }



    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function create(Request $request) {

        $title = 'Add Application';

        $corses = CourseMaster::where('status',1)->get();

        $centers = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();
        
        $gst_centers = CenterMaster::where('gst',1)->where('status',1)->pluck('id')->toarray();

        $gst_courses = CourseMaster::where('gst',1)->where('status',1)->pluck('id')->toarray();

        $registrations = RegistrationForm::where(function ($q) use ($request) {
                                                if(Auth::guard()->user()->is_admin != 1){
                                                    $q->where('center_name',Auth::guard()->user()->branch_id);
                                                }
                                            })->where('roll_no',$request->search)
                                            ->orWhere('mobile_no',$request->search)->first();

        $faculties = FacultyMaster::where(function ($q) use ($request) {
                                        if(Auth::guard()->user()->is_admin != 1){
                                            $q->where('created_by',Auth::guard()->user()->id);
                                        }
                                    })->orderBy('name', 'asc')->get();

        return view('application-form.create', compact('title','corses','centers','registrations','faculties','gst_centers','gst_courses'));



    }



    /**



     * Store a newly created resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @return \Illuminate\Http\Response



     */



    public function store(Request $request) {

        // dd($request->all());

        $message = [

            'course.required' => 'The field is required.',

            'batch_start_date.required' => 'The field is required.',

            'course_fee.required' => 'The field is required.',

            'pay_on_admission.required' => 'The field is required.',

            'batch_end_date.required' => 'The field is required.',

            'scholarship.required' => 'The field is required.',

            'balance.required' => 'The field is required.',

            'batch_time.required' => 'The field is required.',

            'admission_fee.required' => 'The field is required.',

            'number_of_emi.required' => 'The field is required.',

            'course_by_word.required' => 'The field is required.',

            'net_total.required' => 'The field is required.',

            // 'emi.required' => 'The field is required.',

            // 'emi_date.required' => 'The field is required.',

            'cheque_no.required' => 'The field is required.',

            'online_pay_number.required' => 'The field is required.',

            'cheque_date.required' => 'The field is required.',

            'cheque_dwawn.required' => 'The field is required.',

            'job_required.required' => 'The field is required.',

        ];



        $validator = Validator::make($request->all(), [

            'course' => 'required',

            'batch_start_date' => 'required',

            'course_fee' => 'required',

            'pay_on_admission' => 'required',

            'batch_end_date' => 'required',

            'scholarship' => 'required',

            'balance' => 'required',

            'batch_time' => 'required',

            'admission_fee' => 'required',

            // 'number_of_emi' => 'required',

            'course_by_word' => 'required',

            'net_total' => 'required',

            // 'emi' => $request->number_of_emi > 0 ? 'required':'',

            // 'emi_date' => $request->number_of_emi > 0 ? 'required':'',

            'cheque_no' => $request->paid_by == 'cheque' ? 'required':'',

            'cheque_date' => $request->paid_by == 'cheque' ? 'required':'',

            'cheque_dwawn' => $request->paid_by == 'cheque' ? 'required':'',

            'online_pay_number' => $request->paid_by == 'online' ? 'required':'',

            'job_required' => 'required',

        ], $message);



        // if ($validator->fails()) {

        //  return redirect()->back()

        //      ->withErrors($validator)

        //      ->withInput();

        // }



        try {

            $user = User::find(Auth::guard()->user()->id);
            $center_detials = CenterMaster::find($user->branch_id);
            
            $use_amount = $center_detials->use_amount + $request->pay_on_admission;
            
            $center_amount = $center_detials->center_amount;

            if($center_amount >= $request->pay_on_admission){
            
                $app_id = ApplicationForm::insertGetId([

                    'reg_id' => $request->reg_id,

                    'application_id' => $request->application_id,

                    'course' => $request->course,

                    'batch_start_date' => $request->batch_start_date,

                    'course_fee' => $request->course_fee,

                    'pay_on_admission' => $request->pay_on_admission,

                    'batch_end_date' => date('Y-m-d',strtotime($request->batch_end_date)),

                    'scholarship' => $request->scholarship,

                    'balance' => $request->balance,

                    'batch_time' => $request->batch_time,

                    'timeunit' => $request->timeunit,

                    'admission_fee' => $request->admission_fee,

                    'number_of_emi' => $request->number_of_emi,

                    'course_by_word' => $request->course_by_word,

                    'gst_amount' => $request->gst_amount,

                    'net_total' => $request->net_total,

                    'emi' => $request->emi,

                    'emi_date' => $request->emi_date,

                    'faculty_id' => $request->faculty_id,

                    'remarks' => $request->remarks,

                    'paid_by' => $request->paid_by,

                    'cheque_no' => $request->cheque_no,

                    'cheque_date' => $request->cheque_date,

                    'cheque_dwawn' => $request->cheque_dwawn,

                    'online_pay_number' => $request->online_pay_number,

                    'job_required' => $request->job_required,

                    'created_by' => Auth::guard()->user()->id

                ]);



                $app_update = ApplicationForm::where('id', $app_id)

                              ->update(['application_id' => 'APP-'.$app_id]);

                // Credit minus
                $user = User::find(Auth::guard()->user()->id);
                $center_detials = CenterMaster::find($user->branch_id);
                
                $use_amount = $center_detials->use_amount + $request->pay_on_admission;
                
                $center_amount = $center_detials->center_amount - $request->pay_on_admission;
                            
                $course_update = CenterMaster::where('id', $user->branch_id)
                    ->update([
                    'center_amount' => $center_amount,
                    'use_amount' => $use_amount
                ]);
                
                if($request->number_of_emi > 0){
                    $emi_no = 1;
                    for($i=0; $i<$request->number_of_emi; $i++){

                        $emi_data = ApplicationFormEmi::insert([

                            'application_id' => $app_id,

                            'emi_no' => $emi_no,

                            'emi_dates' => $request->emi_dates[$i],

                            'cheque_no' => '',

                            'emi_amounts' => $request->emi_amounts[$i],

                            'payment_type' => '',

                            'created_by' => Auth::guard()->user()->id

                        ]);

                        $emi_no++;

                    }



                }

                // Admin Email
                $to = "somnath@imagic.net.in,admission@imagic.net.in";
                $subject = "Application Form Data";
                $txt = "Registration No : ".Helper::getRegDetails($request->reg_id)->roll_no."\n";
                $txt .= "Application No : ".$request->application_id."\n";
                $txt .= "Course : ".Helper::getCourseName($request->course)."\n";
                $txt .= "Course Fee : ".$request->course_fee."\n";
                $txt .= "Pay on Admission : ".$request->pay_on_admission."\n";
                $txt .= "Scholarship : ".$request->scholarship."\n";
                $txt .= "Admission Fee : ".$request->admission_fee."\n";
                $txt .= "Balance : ".$request->balance."\n"; 
                $txt .= "GST : ".$request->gst_amount."\n";           
                $txt .= "Net Total : ".$request->net_total."\n";
                $txt .= "No. of EMI : ".$request->number_of_emi."\n";
                $txt .= "Paid By : ".$request->paid_by."\n";
                $headers = "From: info@pinealmagic.in";

                // mail($to,$subject,$txt,$headers);

                // Student Email 
                $s_to = $request->email;
                $s_subject = "Application Completed";
                $s_txt = "Congratulation! IMAGIC welcomes you to the best career course. We confirm your admission in ".Helper::getCourseName($request->course).". Best wishes for your bright future! Your application no is : ".$request->application_id;
                $s_headers = "From: info@pinealmagic.in";

                mail($s_to,$s_subject,$s_txt,$s_headers);

                // SMS
                $url = "https://sms11.infonetservices.in/api/api_http.php";
                $your_phone = Helper::getRegDetails($request->reg_id)->mobile_no;
                
                  $param = array(
                      'username' => 'imagic',
                      'password' => 'imagic122',
                      'senderid' => 'iMAGIC',
                      'text' => 'Dear '.Helper::getRegDetails($request->reg_id)->mr_mrs.' '.Helper::getRegDetails($request->reg_id)->first_name.' '.Helper::getRegDetails($request->reg_id)->mid_name.' '.Helper::getRegDetails($request->reg_id)->last_name.', Congratulation! IMAGIC welcomes you to the best career course. We confirm your admission in '.Helper::getCourseName($request->course).'. Best wishes for your bright future!',
                      'route' => 'Informative',
                      'type' => 'text',
                      'datetime' => '2021-02-27 16:30:19',
                      'to' => $your_phone,
                  );

                  $post = http_build_query($param, '', '&');

                  $ch = curl_init();

                  curl_setopt($ch, CURLOPT_URL, $url);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

                  $result = curl_exec($ch);

                  if(curl_errno($ch)) {
                      $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
                  } else {
                      $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                      switch($returnCode) {
                          case 200 :
                              break;
                          default :
                              $result = "HTTP ERROR: " . $returnCode;
                      }
                  }
                  curl_close($ch);



                Session::flash('msg', 'Application sucessfully completed');

                return redirect()->route('application-form.index');
            }else{
                Session::flash('error', "You didn't have credit, please contact to the main office.");
                return redirect()->back();
            }

        } catch (\Exception $e) {

            // dd($e);

            Session::flash('error', $e->getMessage());



            return redirect()->back();



        }



    }









    /**



     * Display the specified resource.



     *



     * @param  int  $id



     * @return \Illuminate\Http\Response



     */



    public function show($id) {



        //



    }



    /**



     * Show the form for editing the specified resource.



     *



     * @param  int  $id



     * @return \Illuminate\Http\Response



     */



    public function edit(Request $request,$id) {

        $title = 'Application Edit';

        $corses = CourseMaster::get();

        $centers = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();

        $gst_centers = CenterMaster::where('gst',1)->where('status',1)->pluck('id')->toarray();

        $gst_courses = CourseMaster::where('gst',1)->where('status',1)->pluck('id')->toarray();

        $apllication = ApplicationForm::find($id);

        $registrations = RegistrationForm::find($apllication->reg_id);

        $faculties = FacultyMaster::where(function ($q) use ($request) {
                                        if(Auth::guard()->user()->is_admin != 1){
                                            $q->where('created_by',Auth::guard()->user()->branch_id);
                                        }
                                    })->orderBy('name', 'asc')->get();

        return view('application-form.edit', compact('title', 'corses','centers','apllication','registrations','faculties','gst_centers','gst_courses'));

    }







    public function update(Request $request, $id) {

        $message = [

            'course.required' => 'The field is required.',

            'batch_start_date.required' => 'The field is required.',

            'course_fee.required' => 'The field is required.',

            'pay_on_admission.required' => 'The field is required.',

            'batch_end_date.required' => 'The field is required.',

            'scholarship.required' => 'The field is required.',

            'balance.required' => 'The field is required.',

            'batch_time.required' => 'The field is required.',

            'admission_fee.required' => 'The field is required.',

            'number_of_emi.required' => 'The field is required.',

            'course_by_word.required' => 'The field is required.',

            'net_total.required' => 'The field is required.',

            'emi.required' => 'The field is required.',

            'emi_date.required' => 'The field is required.',

            'cheque_no.required' => 'The field is required.',

            'online_pay_number.required' => 'The field is required.',

            'cheque_date.required' => 'The field is required.',

            'cheque_dwawn.required' => 'The field is required.',

            'job_required.required' => 'The field is required.',

        ];



        $validator = Validator::make($request->all(), [

            'course' => 'required',

            'batch_start_date' => 'required',

            'course_fee' => 'required',

            'pay_on_admission' => 'required',

            'batch_end_date' => 'required',

            'scholarship' => 'required',

            'balance' => 'required',

            'batch_time' => 'required',

            'admission_fee' => 'required',

            'number_of_emi' => 'required',

            'course_by_word' => 'required',

            'net_total' => 'required',

            'emi' => ($request->number_of_emi > 0 && $request->emi < 1) ? 'required':'',

            'emi_date' => $request->number_of_emi > 0 ? 'required':'',

            'cheque_no' => $request->paid_by == 'cheque' ? 'required':'',

            'cheque_date' => $request->paid_by == 'cheque' ? 'required':'',

            'cheque_dwawn' => $request->paid_by == 'cheque' ? 'required':'',

            'online_pay_number' => $request->paid_by == 'online' ? 'required':'',

            'job_required' => 'required',

        ], $message);



        if ($validator->fails()) {

            return redirect()->back()

                ->withErrors($validator)

                ->withInput();

        }

         \DB::beginTransaction();

        try {



            $application_update = ApplicationForm::where('id', $id)

                ->update([

                'course' => $request->course,

                'batch_start_date' => $request->batch_start_date,

                'course_fee' => $request->course_fee,

                'pay_on_admission' => $request->pay_on_admission,

                'batch_end_date' => date('Y-m-d',strtotime($request->batch_end_date)),

                'scholarship' => $request->scholarship,

                'balance' => $request->balance,

                'batch_time' => $request->batch_time,

                'timeunit' => $request->timeunit,

                'admission_fee' => $request->admission_fee,

                'number_of_emi' => $request->number_of_emi,

                'course_by_word' => $request->course_by_word,

                'gst_amount' => $request->gst_amount,

                'net_total' => $request->net_total,

                'emi' => $request->emi,

                'emi_date' => $request->emi_date,

                'faculty_id' => $request->faculty_id,

                'remarks' => $request->remarks,

                'paid_by' => $request->paid_by,

                'cheque_no' => $request->cheque_no,

                'online_pay_number' => $request->online_pay_number,

                'cheque_date' => $request->cheque_date,

                'cheque_dwawn' => $request->cheque_dwawn,

                'online_pay_number' => $request->online_pay_number,

                'job_required' => $request->job_required,

                'updated_by' => Auth::guard()->user()->id

            ]);

            // dd($request->ids);

            if(!empty($request->ids)){



            }else{

                // dd($request->number_of_emi);

                if($request->number_of_emi > 0){

                    $application = ApplicationFormEmi::where('application_id', $id)->delete();

                    // dd($application);
                    $emi_no = 1;
                    for($i=0; $i<$request->number_of_emi; $i++){

                        $emi_data = ApplicationFormEmi::insert([

                            'application_id' => $id,

                            'emi_no' => $emi_no,

                            'emi_dates' => $request->emi_dates[$i],

                            'cheque_no' => $request->cheque_no[$i],

                            'emi_amounts' => $request->emi_amounts[$i],

                            'payment_type' => $request->payment_type[$i],

                            'created_by' => Auth::guard()->user()->id

                        ]);
                        $emi_no++;

                    }

                }



            }



            \DB::commit();

            Session::flash('msg', 'Application data updated sucessfully');

            return redirect()->route('application-form.index');

        } catch (\Exception $e) {

            \Db::rollback();

            Session::flash('error', $e->getMessage());

            return redirect()->back();



        }





    }



    /**



     * Remove the specified resource from storage.



     *



     * @param  int  $id



     * @return \Illuminate\Http\Response



     */



    public function destroy($id) {

        try {



            $application = ApplicationForm::find($id);



            $application->delete();



            return response()->json(['status' => 200, 'status_text' => 'Application data successfuly deleted']);



        } catch (\Exception $e) {



            \DB::rollback();



            return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);



        }



    }



    public function courseFee($id){

        $course = CourseMaster::find($id);

        $faculties = FacultySubject::where('subject',$id)->get();

        $append_faculties = '<option valiue="">Select</option>';
        foreach ($faculties as $value) {
            $get_faculty = Helper::getFacultyApp($value->faculty_id);
            if($get_faculty){
                $append_faculties .= '<option value="'.$get_faculty->id.'">'.$get_faculty->name.'</option>';
            }
        }

        return response()->json(['course' => $course, 'append_faculties'=>$append_faculties]);

    }

    public function courseEmi($id){

        $course = CourseMaster::find($id);

        return $course->number_of_emi;

    }



    public function courseDuration(Request $request, $id){

        $course = CourseMaster::find($id);

        return date('m/d/Y',strtotime('+'.$course->duration.' days',strtotime($request->batch_start_date))) . PHP_EOL;

    }

    public function applicationFormPdf($id)
    {
        $application = ApplicationForm::find($id);
        $registrations = RegistrationForm::find($application->reg_id);
        $data = [
            'apllication' => $application,
            'registrations' => $registrations
        ];
        // dd($data);

        $pdf = PDF::loadView('pdf.applicationform', $data);
        return $pdf->stream('apllication-form.pdf');
    }

    public function applicationReceiptPdf($id)
    {
        $application = ApplicationForm::find($id);
        $registrations = RegistrationForm::find($application->reg_id);
        //dd($registrations);
        $data = [
            'apllication' => $application,
            'registrations' => $registrations
        ];

        $pdf = PDF::loadView('pdf.applicationreceipt', $data);
        return $pdf->stream('apllication-receipt.pdf');
    }

    public function applicationFacultyPdf($id)
    {
        $application = ApplicationForm::find($id);
        $registrations = RegistrationForm::find($application->reg_id);
        $course_details = CourseMaster::where('id',$application->course)->first();
        $data = [
            'apllication' => $application,
            'registrations' => $registrations,
            'course_details' => $course_details
        ];
        // dd($data);

        $pdf = PDF::loadView('pdf.applicationfaculty', $data);
        return $pdf->stream('apllication-faculty.pdf');
    }

}

