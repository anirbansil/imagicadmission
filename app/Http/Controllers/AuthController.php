<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\CenterMaster;
use Session;
use Carbon\Carbon;  
class AuthController extends Controller {
	public function __construct() {
		$this->middleware('guest')->except('logout');
	}

	public function showLogin() {
		$title = 'Login';
		return view('auth.login', compact('title'));
	}

	public function showAdminLogin() {
		$title = 'login';
		return view('auth.adminlogin', compact('title'));
	}

	public function storeLogin(Request $request) {
		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'password' => 'required',
			
		]);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {

			if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1])) {

				Session::flash("error", "Email and password doesn't match!");
				return back()->withInput();
			}else{
				$user = User::find(Auth::guard()->user()->id);
				$userrole = Auth::guard()->user()->roles;			

				if($userrole[0]->id == 4){
					$center_detials = CenterMaster::find($user->branch_id);
					$credit = $center_detials->center_amount;
					if($credit > 0){
						$login_count = $user->logincount + 1;
						$user_update = User::where('id', $user->id)
						->update([
							'logincount' => $login_count
						]);

						Session::flash("msg", "Welcome ".Auth::guard()->user()->name);
						return redirect()->intended('dashboard');
					}else{
						Auth::logout();
						Session::flash("error", "You didn't have credits, please contact to the main office.");
						return back()->withInput();
					}
				}else{
					Session::flash("msg", "Welcome ".Auth::guard()->user()->name);
					return redirect()->intended('dashboard');
				}

				
			}

			

		}catch (\Exception $e) {
			Session::flash('error', $e->getMessage());
			return redirect()->back();
		}
	}
	
	public function signup() {
		//$this->sendRegistrationOtp();
		$all_course = StudentCourseMaster::get();
		$all_branch = BranchMaster::orderBy('name', 'asc')->get();
		return view('auth.signup', compact('all_course', 'all_branch'));
	}

	public function storeRegistration(Request $request){
		//dd($request->all());die;
			/*$message = [
			'name.required' => 'The name field required.',
			'phone.required' => 'The Phone no field required.',
			'class.required' => 'The class field required.',
			'email.required' => 'The email field required.',
			'center.required' => 'The center field required.',
			
		];*/

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'phone_no' => 'required|numeric|digits:10',
			'class' => 'required',
			'email' => 'required',
			'center' => 'required',
			'course_category' => 'required',
			
		]);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {

			$phone_no_exist = User::where('phone_no',$request->phone_no)->where('isVerified',1)->first();
			if($phone_no_exist){
				Session::flash('error', 'phone number already registered');
				return back()->withInput();
			}else{
				$otp = rand(1000,9000);
				$allRequest = $request->all();
				$check_temp_user = UserTemp::where('phone_no',$request->phone_no)->first();
				if($check_temp_user){
					$check_temp_user->delete();
				}
	            $temp_user = UserTemp::create($allRequest +['otp_code'=>$otp]);
				Session::flash('msg', 'Successfully Registered');
				$otp_send = $this->sendRegistrationOtp($otp,$request->phone_no);
				if($otp_send){
					return redirect()->route('otp_verification', ['otpv' => Crypt::encryptString($temp_user->id)]);
				}else{
					Session::flash('error', "Phone no not valid!");
					//Session::flash("msg", "Reservation No and password doesn't match!");
					return back();
				}
			}
		} catch (\Exception $e) {
			Session::flash('error', $e->getMessage());
			return redirect()->back();
		}
	}



	public function logout() {
		// $user = Auth::user();
		// $userrole = $user->roles()->first()->slug;

		// $user->active_date_time = NULL;
		// $user->save();
		
		Auth::logout();
		
		return redirect()->route('login');
		
	}


}
