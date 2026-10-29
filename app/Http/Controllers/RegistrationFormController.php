<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\InquiryForm;
use App\Models\RegistrationForm;
use App\Models\CourseMaster;
use App\Models\CenterMaster;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use DB;
use Carbon\Carbon;
use Helper;
class RegistrationFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Registration List';
        $registrations = RegistrationForm::where(function ($q) use ($request) {
            if (!empty($request->input('from_date') && !empty($request->input('to_date')))) {
                $datef = Carbon::parse($request->from_date)
                    ->startOfDay()
                    ->toDateTimeString();
                $dateto = Carbon::parse($request->to_date)
                    ->endOfDay()
                    ->toDateTimeString();
                $q->whereBetween('created_at', [$datef, $dateto]);
            }
            if ($request->has('search_by_center') && !empty($request->search_by_center)) {
                $q->where('center_name', $request->search_by_center);
            }
            if ($request->has('search_by_course') && !empty($request->search_by_course)) {
                $q->whereHas('courseName', function ($query) use ($request){
                    $query->where('course',$request->search_by_course);
                });
            }
            if (!empty($request->input('input_search'))) {
                $q->orWhere(DB::raw('CONCAT(first_name, " ", mid_name," ",last_name)'), 'LIKE', '%' . $request->input('input_search') . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $request->input('input_search') . '%')
                    ->orWhere('mid_name', 'LIKE', '%' . $request->input('input_search') . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $request->input('input_search') . '%');
                $q->orWhere('roll_no', 'LIKE', '%' . $request->input('input_search') . '%');
                $q->orWhere('qualification', 'LIKE', '%' . $request->input('input_search') . '%');
            }
            if(Auth::guard()->user()->is_admin != 1){
                //$q->where('center_name',Auth::guard()->user()->branch_id);
				$q->where('created_by', Auth::guard()->id());
            }
        })->orderBy('id', 'desc')->paginate(25)->appends(request()->except('page'));
        $centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();
        $courseDetails = CourseMaster::where('status',1)->get();
        return view('registration-form.index', compact('title', 'registrations', 'centerDetails', 'request','courseDetails'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $title = 'Add registration';
        $corses = CourseMaster::get();
        $centers = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();
        $msg = '';
        $inq_data = '';
        if ($request->search) {
            $search_reg = RegistrationForm::where('inquery_id', $request->search)->first();
            if ($search_reg) {
                $inq_data = '';
                $msg = "This inquery already registered.";
            } else {
                $inq_data = InquiryForm::where('serial_no', $request->search)->first();
            }
        }
        // dd($inq_data);
        return view('registration-form.create', compact('title', 'corses', 'centers', 'inq_data', 'request', 'msg'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $message = [
            'center_name.required' => 'The field is required.',
            'mr_mrs.required' => 'The field is required.',
            'first_name.required' => 'The field is required.',
            'last_name.required' => 'The field is required.',
            'address.required' => 'The field is required.',
            // 'res_address.required' => 'The field is required.',
            'state.required' => 'The field is required.',
            'city.required' => 'The field is required.',
            'district.required' => 'The field is required.',
            'pin_code.required' => 'The field is required.',
            'date_of_birth.required' => 'The field is required.',
            'mobile_no.required' => 'The field is required.',
            'mobile_no.unique' => 'Your mobile number already registered.',
            'email.required' => 'The field is required.',
            'qualification.required' => 'The field is required.',
        ];
        $validator = Validator::make($request->all(), [
            'center_name' => 'required',
            'mr_mrs' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            // 'res_address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'district' => 'required',
            'pin_code' => 'required',
            'date_of_birth' => 'required',
            'mobile_no' => 'required|unique:registration_form',
            'email' => 'required',
            'qualification' => 'required',
        ], $message);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $exits_users = User::where('email',$request->email)->first();
            if ($request->student_photo) {
                $student_photo_image = time() . '-student_photo.' . $request->student_photo->getClientOriginalExtension();
                $request->student_photo->move(public_path('images'), $student_photo_image);
            } else {
                $student_photo_image = '';
            }
            if ($request->id_proff) {
                $id_proff_image = time() . '-id_proff.' . $request->id_proff->getClientOriginalExtension();
                $request->id_proff->move(public_path('images'), $id_proff_image);
            } else {
                $id_proff_image = '';
            }
            if ($request->qualification_certificate) {
                $qualification_certificate_image = time() . '-qualification_certificate.' . $request->qualification_certificate->getClientOriginalExtension();
                $request->qualification_certificate->move(public_path('images'), $qualification_certificate_image);
            } else {
                $qualification_certificate_image = '';
            }
            
            \DB::beginTransaction();
            $registration_id = RegistrationForm::insertGetId([
                'center_name' => $request->center_name,
                'mr_mrs' => $request->mr_mrs,
                'first_name' => $request->first_name,
                'mid_name' => $request->mid_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'res_address' => $request->res_address,
                'state' => $request->state,
                'city' => $request->city,
                'district' => $request->district,
                'pin_code' => $request->pin_code,
                'date_of_birth' => $request->date_of_birth,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'qualification' => $request->qualification,
                'student_photo' => $student_photo_image,
                'id_proff' => $id_proff_image,
                'qualification_certificate' => $qualification_certificate_image,
                'guardian_name' => $request->guardian_name,
                'guardian_mob_no' => $request->guardian_mob_no,
                'inquery_id' => $request->inquery_id ? $request->inquery_id : '',
                'created_by' => Auth::guard()->user()->id
            ]);
            $registration_update = RegistrationForm::where('id', $registration_id)
                ->update(['roll_no' => 'REG-' . $registration_id]);
            // New Use Create 
            $role = Role::where('status', 1)->where('slug','student')->first();
            $password = 'REG-' . $registration_id;
            $name = $request->first_name." ".$request->last_name;
            $user_name = $request->first_name;
            if(!empty($exits_users)){
                $user = User::where('id', $exits_users->id)->update([ 'phone_no' =>$request->mobile_no,'user_name' => $user_name, 'name' => $name,'password' => bcrypt($password),'is_admin'=>2, 'status' => 1,'token'=>'']);
                $user_details = UserDetail::where('user_id',$exits_users->id)->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile' => $request->phone_no,
                ]);
                $exits_users->roles()->attach($role->id);
            }
            else{
                $user = User::create($request->only('email', 'phone_no') + [ 'user_name' => $user_name, 'name' => $name,'password' => bcrypt($password),'is_admin'=>2, 'status' => 1 ,'token'=>''],);
                $user_details = UserDetail::insert(['user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile' => $request->phone_no,
                ]);
                $user->roles()->attach($role->id);
            }
            $s_to = $request->email;
            $s_subject = "Imagic Login Details";
            $s_txt = "Dear  ".$request->first_name." ".$request->last_name.", Congratulation! Welcome to IMAGIC Institute. Here is your login details : Email - ".$request->email." and Password - ".$password.",Regards Imagic";
            $s_headers = "From: info@pinealmagic.in";
            mail($s_to,$s_subject,$s_txt,$s_headers);
            // End new user create

            // Admin Email
            $to = ['aamina.9k@gmail.com','aamina13khan@gmail.com'];
          //  $to = "somnath@imagic.net.in,admission@imagic.net.in";
            $subject = "Registration Form Data";
            $txt = "Name : ".$request->mr_mrs." ".$request->first_name." ".$request->mid_name." ".$request->last_name."\n";
            $txt .= "Phone Number : ".$request->mobile_no."\n";
            $txt .= "Email : ".$request->email."\n";
            $txt .= "Registration Id : REG-".$registration_id."\n";
            $headers = "From: info@pinealmagic.in";
            // mail($to,$subject,$txt,$headers);
            // Student Email
            $s_to = $request->email;
            $s_subject = "Registration Completed";
            $s_txt = "Dear ".$request->mr_mrs." ".$request->first_name." ".$request->mid_name." ".$request->last_name.", Congratulation! Welcome to IMAGIC Institute. We confirm your registration. Wishing you a colour full career. Your registration no is : REG-".$registration_id;
            $s_headers = "From: info@pinealmagic.in";
            mail($s_to,$s_subject,$s_txt,$s_headers);
            // SMS
            $url = "https://sms11.infonetservices.in/api/api_http.php";
            $your_phone = $request->mobile_no;
              $param = array(
                  'username' => 'imagic',
                  'password' => 'imagic122',
                  'senderid' => 'iMAGIC',
                  'text' => 'Dear '.$request->mr_mrs.' '.$request->first_name.' '.$request->mid_name.' '.$request->last_name.', Congratulation! Welcome to IMAGIC Institute. We confirm your registration. Wishing you a colour full career.',
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
            \DB::commit();
            Session::flash('msg', 'Registration sucessfully completed');
            return redirect()->route('registration-form.index');
        } catch (\Exception $e) {
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
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $title = 'Registration Edit';
        $corses = CourseMaster::get();
        $centers = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->where('status',1)->get();
        $registration = RegistrationForm::find($id);
        return view('registration-form.edit', compact('title', 'corses', 'centers', 'registration'));
    }
    public function update(Request $request, $id)
    {
        $message = [
            'center_name.required' => 'The field is required.',
            'mr_mrs.required' => 'The field is required.',
            'first_name.required' => 'The field is required.',
            'last_name.required' => 'The field is required.',
            'address.required' => 'The field is required.',
            // 'res_address.required' => 'The field is required.',
            'state.required' => 'The field is required.',
            'city.required' => 'The field is required.',
            'district.required' => 'The field is required.',
            'pin_code.required' => 'The field is required.',
            'date_of_birth.required' => 'The field is required.',
            'mobile_no.required' => 'The field is required.',
            'email.required' => 'The field is required.',
            'qualification.required' => 'The field is required.',
        ];
        $validator = Validator::make($request->all(), [
            'center_name' => 'required',
            'mr_mrs' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            // 'res_address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'district' => 'required',
            'pin_code' => 'required',
            'date_of_birth' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'qualification' => 'required',
        ], $message);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        \DB::beginTransaction();
        try {
            if ($request->hasFile('student_photo')) {
                $student_photo_image = '';
                if ($request->student_photo) {
                    $student_photo_image = time() . '-student_photo.' . $request->student_photo->getClientOriginalExtension();
                    $request->student_photo->move(public_path('images'), $student_photo_image);
                    $update_student_photo = RegistrationForm::where('id', $id)
                        ->update([
                            'student_photo' => $student_photo_image
                        ]);
                }
            }
            if ($request->hasFile('id_proff')) {
                $id_proff_image = '';
                if ($request->id_proff) {
                    $id_proff_image = time() . '-id_proff.' . $request->id_proff->getClientOriginalExtension();
                    $request->id_proff->move(public_path('images'), $id_proff_image);
                    $update_id_proff = RegistrationForm::where('id', $id)
                        ->update([
                            'id_proff' => $id_proff_image
                        ]);
                }
            }
            if ($request->hasFile('qualification_certificate')) {
                $qualification_certificate_image = '';
                if ($request->qualification_certificate) {
                    $qualification_certificate_image = time() . '-qualification_certificate.' . $request->qualification_certificate->getClientOriginalExtension();
                    $request->qualification_certificate->move(public_path('images'), $qualification_certificate_image);
                    $update_qualification_certificate = RegistrationForm::where('id', $id)
                        ->update([
                            'qualification_certificate' => $qualification_certificate_image
                        ]);
                }
            }
            $registration_update = RegistrationForm::where('id', $id)
                ->update([
                    'center_name' => $request->center_name,
                    'mr_mrs' => $request->mr_mrs,
                    'first_name' => $request->first_name,
                    'mid_name' => $request->mid_name,
                    'last_name' => $request->last_name,
                    'address' => $request->address,
                    'res_address' => $request->res_address,
                    'state' => $request->state,
                    'city' => $request->city,
                    'district' => $request->district,
                    'pin_code' => $request->pin_code,
                    'date_of_birth' => $request->date_of_birth,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'qualification' => $request->qualification,
                    'guardian_name' => $request->guardian_name,
                    'guardian_mob_no' => $request->guardian_mob_no,
                    'updated_by' => Auth::guard()->user()->id
                ]);
            \DB::commit();
            Session::flash('msg', 'Registration data updated sucessfully');
            return redirect()->route('registration-form.index');
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
    public function destroy($id)
    {
        try {
            $registration = RegistrationForm::find($id);
            $registration->delete();
            return response()->json(['status' => 200, 'status_text' => 'Registration data successfuly deleted']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);
        }
    }
    public function OutsiderRegistrationForm(Request $request )  {
        $title ="Imagic | Registration Form";
        $centers = CenterMaster::where('status',1)->get();
            return view('outsider-registration.create', compact('title','centers'));

    }
    public function OutsiderRegistrationStore(Request $request)  {
        $role = Role::where('status', 1)->where('slug','outsider_student')->first();
        $message = [
			'first_name.required' => 'The first name field is required.',
			'last_name.required' => 'The last name field is required.',
			'email.required' => 'Email field is required.',
			// 'email.unique' => 'This Email already exist.',
			'phone_no.required' => 'Phone Number field is required.',
			'phone_no.min' => 'Minimum 8 digit number is required.',
			'password.required' => 'Password field is required',
		];
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
            'email' => 'required | email ',
			// 'email' => 'required | email |unique:users',
			'phone_no' => 'required|min:8',
			'password' => 'required|confirmed|min:6',
		], $message);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
        $exits_users = User::where('email',$request->email)->first();
        if(!empty($exits_users) && $exits_users->status == 1 && $exits_users->otp_verified == 1 ){
            Session::flash('error', 'This Email is already registered.');
			return redirect()->back();
        }
        try {
            $otp = mt_rand(1000, 9999);
            //$otp = 1234;
			$name = $request->first_name.' '.$request->last_name;
			$user_name = $request->first_name.'-'.$request->last_name;
            $token = md5($request->phone_no.time());
            if(!empty($exits_users) && $exits_users->status == 0 && $exits_users->otp_verified == 0 ){
                $user = User::where('id', $exits_users->id)->update([ 'phone_no' =>$request->phone_no,'user_name' => $user_name, 'name' => $name,'password' => bcrypt($request->password),'is_admin'=>2, 'status' => 0 ,'otp'=>$otp ,'otp_verified'=>0 ,'token'=>$token]);
                $user_details = UserDetail::where('user_id',$exits_users->id)->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile' => $request->phone_no,
                ]);
                $user->roles()->attach(7);

            }else{
                $user = User::create($request->only('email', 'phone_no') + [ 'user_name' => $user_name, 'name' => $name,'password' => bcrypt($request->password),'is_admin'=>2, 'status' => 0 ,'otp'=>$otp ,'otp_verified'=>0 ,'token'=>$token],);
                $user_details = UserDetail::insert(['user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile' => $request->phone_no,
                ]);
                $user->roles()->attach(7);
            }
			// dd($role->id);
            $s_to = $request->email;
            $s_subject = "Registration on Imagic";
            $s_txt = "Dear  ".$request->first_name." ".$request->last_name.", Congratulation! Welcome to IMAGIC Institute. Please provide this otp ".$otp." for verification,Regards Imagic";
            $s_headers = "From: info@pinealmagic.in";
            mail($s_to,$s_subject,$s_txt,$s_headers);
            $url = "https://sms11.infonetservices.in/api/api_http.php";
            $your_phone = $request->phone_no;
              $param = array(
                  'username' => 'imagic',
                  'password' => 'imagic122',
                  'senderid' => 'iMAGIC',
                  'text' => 'Dear '.$request->first_name.' '.$request->last_name.' , Congratulation! Welcome to IMAGIC Institute. Please provide this otp '.$otp.' for verification,Regards Imagic ',
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
			return redirect()->route('registration.verification',['vefication_token'=>$token]);
		} catch (\Exception $e) {
			Session::flash('error', $e->getMessage());
			return redirect()->back();
		}
    }
    public function RegistrationVerification(Request $request) {
        $title ="Imagic | Registration Verification";
        $token = $request->vefication_token;
        Session::flash('msg', 'Please Verify your phone number');
        return view('outsider-registration.otp-verification' ,compact('title','token'));
    }

    public function OtpVerification(Request $request)  {
        $message = [
			'otp.required' => 'The otp is required.',
		];
		$validator = Validator::make($request->all(), [
			'otp' => 'required',
		], $message);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
        try {
            $user = User::where(['otp'=>$request->otp,'token'=>$request->token])->first();
            if(!empty($user)){
                $user->otp=0;
                $user->otp_verified=1;
                $user->status=1;
                $user->save();

                $s_to = $user->email;
                $s_subject = "Imagic Login Details";
                $s_txt = "Dear  ".$user->first_name." ".$user->last_name.", Congratulation! Welcome to IMAGIC Institute. Here is your login details : Email - ".$user->email." and Password - ".$user->password.",Regards Imagic";
                $s_headers = "From: info@pinealmagic.in";
                mail($s_to,$s_subject,$s_txt,$s_headers);

                Session::flash('msg', "Welcome to Imagic Registration Completed Please login with Email and Passowrd");
                return redirect('/');
            }else{
                Session::flash('msg', 'Otp Does not match');
                return redirect()->back();
            }
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
			return redirect()->back();
        }

    }
}
