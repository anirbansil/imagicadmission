<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\InquiryForm;

use App\Models\RegistrationForm;

use App\Models\User;

use App\Models\CourseMaster;

use App\Models\CenterMaster;

use App\Models\ApplicationForm;

use App\Models\ApplicationFormEmi;

use App\Models\CheckinCheckout;

use App\Models\Role;

use App\Models\UserDetail;

use App\Exports\AttendenceExport;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\CourseModule;

use Auth;



use DB;

use Carbon\Carbon;

use ConsoleTVs\Charts\ChartsController;



class DashboardController extends Controller {



    public function index(Request $request) {



        // dd($request->all());



        $title = "Dashboard";



        $users = User::where('is_admin','!=',1)->get()->count();



        $user_counts = User::where('is_admin','!=',1)->count();


        $total_inquiry = InquiryForm::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                // $q->where('center_name',Auth::guard()->user()->branch_id);
                                $q->where('created_by', Auth::guard()->id());

                            }

                        })->whereMonth('created_at', Carbon::now()->month)->count();



        $today_inquiry = InquiryForm::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                // $q->where('center_name',Auth::guard()->user()->branch_id);
                                $q->where('created_by', Auth::guard()->id());

                            }

                        })->whereDate('created_at', Carbon::today())->count();



        $today_hot_inquiry = InquiryForm::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                // $q->where('center_name',Auth::guard()->user()->branch_id);
                                $q->where('created_by', Auth::guard()->id());

                            }

                        })->where('status',1)->whereDate('date_of_reg', Carbon::today())->count();



        $total_courses = CourseMaster::count();





        $inquery = InquiryForm::where(function ($q) use ($request) {



            if ( !empty($request->input('from_date')) && !empty($request->input('to_date')) )

            {



                $datef = Carbon::parse($request->from_date)

                    ->startOfDay()

                    ->toDateTimeString();



                $dateto = Carbon::parse($request->to_date)

                    ->endOfDay()

                    ->toDateTimeString();

                $q->whereBetween('created_at', [$datef, $dateto]);

            }

            else{

                $q->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);

            }

            if(Auth::guard()->user()->is_admin != 1){

                $q->where('center_name',Auth::guard()->user()->branch_id);

            }



        })->count();



        $registration = RegistrationForm::where(function ($q) use ($request) {



            if ( !empty($request->input('from_date')) && !empty($request->input('to_date')) )

            {



                $datef = Carbon::parse($request->from_date)

                    ->startOfDay()

                    ->toDateTimeString();



                $dateto = Carbon::parse($request->to_date)

                    ->endOfDay()

                    ->toDateTimeString();

                $q->whereBetween('created_at', [$datef, $dateto]);

            }

            else{

                $q->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);

            }

            if(Auth::guard()->user()->is_admin != 1){

                $q->where('center_name',Auth::guard()->user()->branch_id);

            }



        })->count();





        $regData = '';



        $regData.="['inquery',  ".$inquery.", 'color: gray'], ['registration',  ".$registration.", 'color: gray'],";



        $regInqChartData = $regData;



        //   dd($regInqChartData);



        // MCR Report

        $applications_mcr = ApplicationForm::where(function($q) use ($request) {

                            if (!empty($request->input('from_date') && !empty($request->input('to_date'))))

                            {



                                $datef = Carbon::parse($request->from_date)

                                         ->startOfDay() 

                                         ->toDateTimeString();



                                $dateto = Carbon::parse($request->to_date)

                                         ->endOfDay()

                                         ->toDateTimeString();

                                $q->whereBetween('created_at',[$datef, $dateto]);

                            }



                            if (!empty($request->input('search')))

                            {

                                $reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();

                                if($reg_id){

                                    $q->where('reg_id',$reg_id->id);    

                                }else{

                                    $q->where('reg_id',0);

                                }

                            }



                            if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))

                            {

                                $q->whereMonth('created_at', Carbon::now()->month);

                            }

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->whereHas('regDetails', function ($qu) use ($request) {

                                    $qu->where('center_name',Auth::guard()->user()->branch_id);

                                });

                                

                            }

                        })                      

                        ->orderBy('id', 'desc')

                        ->get();

        $applications_emi_mcr = ApplicationFormEmi::where(function($q) use ($request) {

                                if (!empty($request->input('from_date') && !empty($request->input('to_date'))))

                                {



                                    $datef = Carbon::parse($request->from_date)

                                             ->startOfDay() 

                                             ->toDateTimeString();



                                    $dateto = Carbon::parse($request->to_date)

                                             ->endOfDay()

                                             ->toDateTimeString();

                                    $q->whereBetween('updated_at',[$datef, $dateto]);

                                }



                                if (!empty($request->input('search')))

                                {

                                    $reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();

                                    

                                    if($reg_id){

                                        $app_id = ApplicationForm::where('reg_id',$reg_id->id)->first();

                                        $q->where('application_id',$app_id->id);    

                                    }else{

                                        $q->where('application_id',0);  

                                    }

                                    

                                }



                                if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))

                                {

                                    $q->whereMonth('updated_at', Carbon::now()->month);

                                }

                                if(Auth::guard()->user()->is_admin != 1){

                                    $q->whereHas('application', function ($qu) use ($request) {

                                        $qu->whereHas('regDetails', function ($qur) use ($request) {

                                            $qur->where('center_name',Auth::guard()->user()->branch_id);

                                        });

                                        

                                    });

                                    

                                }

                            })

                            ->groupBy('application_id')

                            ->orderBy('id', 'desc')

                            ->get();



        $app_total = 0; $emi_total = 0; $extra = 0;



        foreach($applications_mcr as $application){

            $app_total = $app_total + $application->pay_on_admission;

        }



        foreach($applications_emi_mcr as $emi){

            $emi_total = $emi_total + $emi->emi_amounts + $emi->extra_amount;

            $extra = $extra + @$emi->extra_amount;

        }



        $total_mcr = $app_total + $emi_total;



        $applications_msr = ApplicationForm::where(function($q) use ($request) {

                            if (!empty($request->input('from_date') && !empty($request->input('to_date'))))

                            {



                                $datef = Carbon::parse($request->from_date)

                                         ->startOfDay() 

                                         ->toDateTimeString();



                                $dateto = Carbon::parse($request->to_date)

                                         ->endOfDay()

                                         ->toDateTimeString();

                                $q->whereBetween('created_at',[$datef, $dateto]);

                            }



                            if (!empty($request->input('search')))

                            {

                                $reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();

                                if($reg_id){

                                    $q->where('reg_id',$reg_id->id);    

                                }else{

                                    $q->where('reg_id',0);

                                }

                            }



                            if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))

                            {

                                $q->whereMonth('created_at', Carbon::now()->month);

                            }

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->whereHas('regDetails', function ($qu) use ($request) {

                                    $qu->where('center_name',Auth::guard()->user()->branch_id);

                                });

                                

                            }

                        })                      

                        ->orderBy('id', 'desc')

                        ->get();



        $app_total_msr = 0; 



        foreach($applications_msr as $application_msr) {

            $app_total_msr = $app_total_msr + $application_msr->net_total;

        }              

        $total_msr = $app_total_msr;



        $regDataBar = '';

        $regDataBar.="['Monthly Collection Report',  ".$total_mcr.", 'color: green'], ['Monthly Sales Report',  ".$total_msr.", 'color: gray'],";

        $regInqBarChartData = $regDataBar;

        $courses = CourseMaster::all();

        $today=Carbon::today();

        $date = $today->toDateString();

        $checkin = CheckinCheckout::where(['user_id' =>Auth::user()->id ,'date'=>$date ,'checkout_time'=>null])->orderBy('id', 'DESC')->first();

        $admin = Auth::guard()->user();
        $roleArray = $admin->roles()->pluck('slug')->toArray();

        if($roleArray[0]== "student"){
            // $reg_id = explode('-',Auth::guard()->user()->user_name);
            // $reg_no = 'REG-'.$reg_id[1];
            $registrations = RegistrationForm::where('email',Auth::guard()->user()->email)->latest()->first();
            $application = ApplicationForm::where('reg_id',$registrations->id)->latest()->first();
        }else{
            $application = '';
            $registrations = '';
        }

        // Placement
        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => "http://imagic.net.in/wp-json/wp/v2/placement?_embed",// your preferred link

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_TIMEOUT => 30000,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // Set Here Your Requesred Headers

                'Content-Type: application/json',

            ),

        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);
        
        $placements = json_decode($response);

        // jobs

        $curl1 = curl_init();

        curl_setopt_array($curl1, array(

            CURLOPT_URL => "http://imagic.net.in/wp-json/wp/v2/posts?_embed&categories=4&page=1",// your preferred link

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_TIMEOUT => 30000,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // Set Here Your Requesred Headers

                'Content-Type: application/json',

            ),

        ));

        $response1 = curl_exec($curl1);

        $err1 = curl_error($curl1);

        curl_close($curl1);

        $jobs = json_decode($response1);

        $user = User::find(Auth::guard()->user()->id);
        $userroles = Auth::guard()->user()->roles;
        $user_role = $userroles[0]->id;
        $center_detials = CenterMaster::find($user->branch_id);
        
        return view('dashboard', compact('title','users','request','user_counts', 'regInqChartData','regInqBarChartData','inquery','registration','total_inquiry','today_inquiry','total_courses','today_hot_inquiry' ,'courses' ,'checkin','application','registrations','placements','jobs','user_role','center_detials'));



    }



    public function Checkin(Request $request) {

        try {

            $today=Carbon::today();

            $date = $today->toDateString();

            $year=$today->year;

            $month=$today->month;

            $day=$today->day;

            $time = Carbon::now('Asia/Kolkata')->format('H:i:s');

            $check_in = CheckinCheckout::create([

                'user_id' =>Auth::user()->id,

                'course' =>$request->course,

                'module' =>$request->module ,

                'date'=>$date,

                'check_in_time' =>$time,

            ]);

           return  redirect()->back();

        } catch (\Exception $e) {

            throw $e;

        }

        

    }



    public function Checkout(Request $request,$id) {

        try {

            $time = Carbon::now('Asia/Kolkata')->format('H:i:s');

           $check_in = CheckinCheckout::where('id',$id)->update([

            'checkout_time' =>$time,

           ]);

           return  redirect()->back();

        } catch (\Exception $e) {

            throw $e;

        }

        

    }



    public function CheckinCheckoutList(Request $request){

        $title ="Check-in : Check-out List";

        if (Auth::guard()->user()->is_admin == 1 ){

        $checkin_list = CheckinCheckout::where(function ($q) use ($request) {

            if ($request->has('course_name') && !empty($request->course_name) ){

                $q->where('course', $request->course_name);

            }

            if (!empty($request->input('module_name') && !empty($request->module_name)) ){

                $q->where('module',  'LIKE', '%' . $request->module_name . '%');

            }

            })->orderBy('id', 'DESC')->paginate(10);

        }else{

                $checkin_list = CheckinCheckout::where('user_id',Auth::guard()->user()->id)->orderBy('id', 'DESC')->paginate(10);

        }

        if ($request->has('course_name') && !empty($request->course_name) ){

        $module = CourseModule::where('course_id',$request->course_name)->get();

        }else{

            $module ='';

        }



           // dd($students);

        $today=Carbon::today();

        $date = $today->toDateString();

        $checkin = CheckinCheckout::where(['user_id' =>Auth::user()->id ,'date'=>$date ,'checkout_time'=>null])->orderBy('id', 'DESC')->first();

        if(Auth::guard()->user()->roles[0]->slug == 'student'){

            $student_course = ApplicationForm::where(function ($q) use ($request) {

                $q->whereHas('regDetails', function ($qu) use ($request) {

                    $qu->where('email', Auth::guard()->user()->email);

                });

            })->first();

            @$courses = CourseMaster::where('id',@$student_course->course)->get();

        }else{

            $courses = CourseMaster::all();

        }
        	$users_role = Role::get();
        return view('CheckinCheckoutList',compact('title','checkin_list','courses','module' ,'request','checkin','users_role'));

    }



    public function CheckinCheckoutListExport(Request $request){

        $title ="Check-in : Check-out List";

        $checkin_list = CheckinCheckout::where(function ($q) use ($request) {
            if ($request->has('course_name') && !empty($request->course_name) ){
                $q->where('course', $request->course_name);
            }
            if ($request->has('module_name')  && !empty($request->module_name)){

                $q->where('module',  'LIKE', '%' . $request->module_name . '%');
            }
            })->orderBy('id', 'DESC')->get();
            $data = [
                'checkin_list' => $checkin_list
            ];
            return Excel::download(new AttendenceExport($data), 'attendance-list.xlsx');

    }



    public function studentlogin(Request $request){

        try{

        $role = Role::where('status', 1)->where('slug','student')->first();

        $registrations = RegistrationForm::get();
        
        foreach ($registrations as $key => $value) {

            $name=$value->mr_mrs.' '.$value->first_name.' '.$value->mid_name.' '.$value->last_name;

            $user_name = $value->first_name.$value->roll_no;

            $password =$value->mobile_no;

            $add_user = User::create([

                'name' => $name,

                'user_name' => $user_name,

                'email' => $value->email,

                'phone_no' => $value->mobile_no,

                'password' => bcrypt($password),

                'branch_id' => $value->center_name,

                'is_admin' => 2,

                'status' => 1,

            ]);

            $user_details = UserDetail::insert(['user_id' => $add_user->id,

                'first_name' => $value->first_name,

                'last_name' => $value->last_name,

                'mobile' => $value->mobile_no,

            ]);

            $add_user->roles()->attach($role->id);

        }

            return 'Sucessfully Done';

        }catch (\Exception $e) {

            return  $e->getMessage();

        }

    }



    public function placement(){



        $curl = curl_init();



        curl_setopt_array($curl, array(

            CURLOPT_URL => "http://imagic.net.in/wp-json/wp/v2/placement?_embed",// your preferred link

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_TIMEOUT => 30000,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // Set Here Your Requesred Headers

                'Content-Type: application/json',

            ),

        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);



        if ($err) {

            dd($err);

        } else {

            $title = "Placements";

            $data = json_decode($response);

            // dd($data);

            return view('placements', compact('title','data'));

        }

        

        

    }



    public function jobs(Request $request){



        $curl = curl_init();



        curl_setopt_array($curl, array(

            CURLOPT_URL => "http://imagic.net.in/wp-json/wp/v2/posts?_embed&categories=4&page=1",// your preferred link

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_TIMEOUT => 30000,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // Set Here Your Requesred Headers

                'Content-Type: application/json',

            ),

        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);



        if ($err) {

            dd($err);

        } else {

            $title = "Jobs";

            $data = json_decode($response);



            curl_setopt_array($curl, array(

            CURLOPT_URL => "http://imagic.net.in/wp-json/wp/v2/posts?_embed&categories=4&page=1",// your preferred link

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_TIMEOUT => 30000,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // Set Here Your Requesred Headers

                'Content-Type: application/json',

            ),

            ));

            $response1 = curl_exec($curl);

            $err1 = curl_error($curl);

            curl_close($curl);

            $total_data = count(json_decode($response1));

            

            $page = $request->page;

            return view('jobs', compact('title','data','total_data','page'));

        }

        

        

    }
    	public function UsersByrole(Request $request,$id) {
		$users = User::where(function ($q) use ($id) {
                               $q->whereHas('roles', function ($qu) use ($id) {
            						$qu->where('role_id', $id);
            					});
		})->where('is_admin', '!=', 1)->orderBy('id', 'desc')->get();
		return response()->json(['status' => 200, 'users' =>$users]);
	}



    // http://imagic.net.in/wp-json/wp/v2/posts?categories=4&page=1



    



}







