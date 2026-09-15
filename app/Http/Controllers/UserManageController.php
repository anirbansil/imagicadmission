<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\CenterMaster;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;
use Hashids\Hashids;
 
class UserManageController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function index(Request $request) {

		$title = 'Users List';

		$users = User::where(function ($q) use ($request) {
							if(Auth::guard()->user()->is_admin != 1){
				                $q->where('center_name',Auth::guard()->user()->branch_id);
				            }
				            if ($request->has('search_by_role') && !empty ($request->search_by_role)) {
                               $q->whereHas('roles', function ($qu) use ($request) {
            						$qu->where('role_id', $request->search_by_role);
            					});
                            }
                            if ($request->has('input_search') && !empty ($request->input_search)) {
                                $q->where(function ($que) use ($request) {
                                    $que->orWhere('name', 'like', '%' . $request->input_search . '%');
                                    $que->orWhere('user_name', 'like', '%' . $request->input_search . '%');
                                    $que->orWhere('email', 'like', '%' . $request->input_search . '%');
                                    $que->orWhere('phone_no', 'like', '%' . $request->input_search . '%');
                                });
                            }
						})->where('is_admin', '!=', 1)->orderBy('id', 'desc')->paginate(10);		

		$roles = Role::get();
		$users_role = \DB::table('role_users')->get();

		// dd($users_role);

		return view('user-manage.index', compact('title', 'users','roles','request'));

	}

	/**

	 * Show the form for creating a new resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function create() {

		$title = 'User create';

		$allRole = Role::where('status', 1)->get();

		$centers = CenterMaster::where('status',1)->get();

		return view('user-manage.create', compact('title', 'allRole','centers'));

	}

	/**

	 * Store a newly created resource in storage.

	 *

	 * @param  \Illuminate\Http\Request  $request

	 * @return \Illuminate\Http\Response

	 */

	public function store(Request $request) {
		

		$message = [

			'first_name.required' => 'The first name field is required.',

			'last_name.required' => 'The last name field is required.',

			'email.required' => 'Email field is required.',

			'email.unique' => 'This Email already exist.',

			'phone_no.required' => 'Phone Number field is required.',

			'phone_no.min' => 'Minimum 8 digit number is required.',

			'roles.required' => 'The role field is required.',

			// 'branch_id.required' => 'Email field is required.',

			'password.required' => 'Password field is required',

			// 'password_confirmation.required' => 'Password field is required',
		];

		$validator = Validator::make($request->all(), [

			'first_name' => 'required',

			'last_name' => 'required',

			'email' => 'required | email |unique:users',

			'phone_no' => 'required|min:8',

			'roles' => 'required',

			// 'branch_id' => 'required',

			'password' => 'required|confirmed|min:6',

			// 'password_confirmation' => 'required',

		], $message);

		if ($validator->fails()) {
			return redirect()->back()

				->withErrors($validator)

				->withInput();

		}

		try {
			
			$name = $request->first_name.' '.$request->last_name;
			$user_name = $request->first_name.'-'.$request->last_name;

			$user = User::create($request->only('email', 'phone_no','branch_id') + [ 'user_name' => $user_name, 'name' => $name,'password' => bcrypt($request->password),'is_admin'=>2, 'status' => 1]);	
			$imageName = '';

			if ($request->profile_image) {

				$imageName = time() . '.' . $request->profile_image->getClientOriginalExtension();
				$request->profile_image->move(public_path('images'), $imageName);
			}
			

			$user_details = UserDetail::insert(['user_id' => $user->id,

				'first_name' => $request->first_name,

				'last_name' => $request->last_name,

				'address' => $request->address,

				'mobile' => $request->phone_no,

				'profile_image' => $imageName]);

			if ($request->has('roles')) {
				$user->roles()->attach($request->input('roles'));
			}
			
			Session::flash('msg', 'User Successfully created');

			return redirect()->route('user-manage.index');

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

	public function show($id) {

		//

	}

	/**

	 * Show the form for editing the specified resource.

	 *

	 * @param  int  $id

	 * @return \Illuminate\Http\Response

	 */

	public function edit($id) {

		$title = 'User Edit';

		//$hashids = new Hashids();
		// $id = decrypt($id);

		$user = User::find($id);
		$allRole = Role::get();

		$centers = CenterMaster::where('status',1)->get();

		return view('user-manage.edit', compact('title', 'user', 'allRole','centers'));

	}

	

	public function update(Request $request, $id) {


        $message = [

            'first_name.required' => 'The first name field is required.',

            'last_name.required' => 'The last name field is required.',

            'email.required' => 'Email field is required.',

            'email.unique' => 'This Email exist.',

            'phone_no.required' => 'Phone Number field is required.',

            'phone_no.min' => 'Minimum 8 digit number is required.',

            'roles.required' => 'The role field is required.',

            // 'branch_id.required' => 'Email field is required.',

            'password.required' => 'Password field is required',

            // 'password_confirmation.required' => 'Password field is required',
        ];

        $validator = Validator::make($request->all(), [

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => !empty($request->email) ? 'required|email|unique:users,email,' . $id : '',

            'phone_no' => 'required|min:8',

            'roles' => 'required',

            // 'branch_id' => 'required',

            'password' => !empty($request->chng_pass) ? 'confirmed|min:6|required' : '',

            // 'password_confirmation' => 'required',

        ], $message);

		if ($validator->fails()) {

			return redirect()->back()

				->withErrors($validator)

				->withInput();

		}



		try {

			$alldata = $request->except(['roles', 'password', 'profile_image', 'user_id']);

			$user = User::find($id);

			$user->fill($alldata);

			$user->save();

			// if ($request->has('password')) {
			// 	$user->password = \Hash::make($request->password);
			// 	$user->save();
			// }

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
                $user->save();
            }

			if ($request->hasFile('profile_image')) {
				$imageName = '';
				if ($request->profile_image) {
					$imageName = time() . '.' . $request->profile_image->getClientOriginalExtension();
					$request->profile_image->move(public_path('images'), $imageName);
					$user_details = UserDetail::where('user_id', $id)
						->update([

							'profile_image' => $imageName]);
				}
			}
			$user->roles()->detach();
			$user->roles()->attach($request->roles);

			$user_details = UserDetail::where('user_id', $id)
				->update([

					'first_name' => $request->first_name,

					'last_name' => $request->last_name,

					'address' => $request->address]);

			Session::flash('msg', 'User successfully updated');

			return redirect()->route('user-manage.index');

		} catch (\Exception $e) {
			Session::flash('error', $e->getMessage());
            // dd(session('error'));
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

			$user = User::find($id);

			$user_details = UserDetail::where('user_id', $id);

			$user->delete();

			$user_details->delete();

			

			// \DB::commit();

			// // echo "string"; die();

			return response()->json(['status' => 200, 'status_text' => 'Successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}

	public function profile() {
		$title = 'Profile';
		$user = Auth::guard()->user();
		//$allRole = Role::where('status', 1)->get();
        //dd($user);

		return view('user-manage.user-profile', compact('title', 'user'));
	}

	public function profile_update(Request $request) {


		$user = Auth::guard()->user();
		$id = $user->id;


        $message = [

            'first_name.required' => 'The first name field is required.',

            'last_name.required' => 'The last name field is required.',

            'email.required' => 'Email field is required.',

            'email.unique' => 'This Email exist.',

            'phone_no.required' => 'Phone Number field is required.',

//            'roles.required' => 'The role field is required.',

            'password.required' => 'Password field is required',

            // 'password_confirmation.required' => 'Password field is required',
        ];

        $validator = Validator::make($request->all(), [

            'first_name' => 'required',

            'last_name' => 'required',

            'email' => 'required | email',

            'phone_no' => 'required',

//            'roles' => 'required',

            'password' => !empty($request->chng_pass) ? 'confirmed|min:6|required' : '',

        ], $message);

		if ($validator->fails()) {
			dd($validator);
			return redirect()->back()

				->withErrors($validator)

				->withInput();

		}


		try {
			
			$alldata = $request->except(['password', 'profile_image', 'user_id']);

			$user = User::find($id);

			$user->fill($alldata);

			$user->save();
			
			if (!empty($request->password)) {
				$user->password = bcrypt($request->password);
				$user->save();
			}

			if ($request->hasFile('profile_image')) {
				$imageName = '';
				if ($request->profile_image) {
					$imageName = time() . '.' . $request->profile_image->getClientOriginalExtension();
					$request->profile_image->move(public_path('images'), $imageName);

//					$user_details = UserDetail::where('user_id', $id)
//						->update([
//
//							'profile_image' => $imageName]);

                    $user_details = UserDetail::updateOrCreate([
                        'user_id'   => $id,
                    ],[
                        'profile_image' => $imageName
                    ]);
				}
			}
			
			

            $user_details = UserDetail::updateOrCreate([
                'user_id'   => $id,
            ],[
                'first_name' => $request->first_name,

				'last_name' => $request->last_name,

				'address' => $request->address,

				'mobile' => $request->phone_no
            ]);

            
            Session::flash('msg', 'Successfully updated');

			return redirect()->route('user-manage.user-profile');

		} catch (\Exception $e) {

			Session::flash('error', $e->getMessage());

			return redirect()->back();

		}

	}

	public function rand_string($length) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars), 0, $length);

	}

}
