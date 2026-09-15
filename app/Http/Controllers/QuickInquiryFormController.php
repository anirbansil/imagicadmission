<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\QuickInquiryForm;

use App\Models\CourseMaster;

use App\Models\CenterMaster;

use Auth;

use Illuminate\Http\Request;

use Session;

use Validator;

use Carbon\Carbon;

use DB;

use Helper;

use GuzzleHttp\Client;


class QuickInquiryFormController extends Controller {


	public function index(Request $request)

    {

		$title = 'Quick Inquiry List';



        $inquires = QuickInquiryForm::where(function ($q) use ($request) {



            if (!empty($request->input('from_date') && !empty($request->input('to_date')))) {

                $datef = Carbon::parse($request->from_date)

                    ->startOfDay()

                    ->toDateTimeString();

                $dateto = Carbon::parse($request->to_date)

                    ->endOfDay()

                    ->toDateTimeString();

                $q->whereBetween('created_at', [$datef, $dateto]);

            }



            if ($request->has('dashboard') && !empty($request->dashboard) && ($request->dashboard ==1)) {

                $q->whereDate('created_at', Carbon::today());

            }



            if ($request->has('dashboard') && !empty($request->dashboard) && ($request->dashboard ==2)) {

                $q->whereDate('date_of_reg', Carbon::today());

                $q->where('status', 1);

            }



            if ($request->has('dashboard') && !empty($request->dashboard) && ($request->dashboard ==3)) {

                $q->whereMonth('created_at', Carbon::now()->month);

                $q->where('status', 1);

            }

            if ($request->has('search_by_year') && !empty($request->search_by_year)) {

                $q->whereYear('created_at', $request->search_by_year);

            }

            if ($request->has('search_by_center') && !empty($request->search_by_center)) {

                $q->where('center_name', $request->search_by_center);

            }

            if ($request->has('search_by_course') && !empty($request->search_by_course)) {

                $q->where('course', $request->search_by_course);

            }

            if ($request->has('status') && !empty($request->status)) {

                $q->where('status', $request->status);

            }



            if (!empty($request->input('input_search'))) {

                $q->orWhere(DB::raw('CONCAT(first_name, " ", mid_name," ",last_name)'), 'LIKE', '%' . $request->input('input_search') . '%')

                    ->orWhere('first_name', 'LIKE','%' . $request->input('input_search') . '%')

                    ->orWhere('mid_name', 'LIKE','%' . $request->input('input_search') . '%')

                    ->orWhere('last_name', 'LIKE','%' . $request->input('input_search') . '%');

                $q->orWhere('mobile_no', 'LIKE', '%' . $request->input('input_search') . '%');

                $q->orWhere('qualification', 'LIKE', '%' . $request->input('input_search') . '%');

            }
            
            if (!empty($request->input('serial_no'))) {
                
                $q->where('serial_no', $request->input('serial_no'));
                
            }

            if(Auth::guard()->user()->is_admin != 1){

                $q->where('center_name',Auth::guard()->user()->branch_id);

            }



        })->orderBy('id', 'desc')->paginate(25)->appends(request()->query());



        $centerDetails = CenterMaster::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->where('id',Auth::guard()->user()->branch_id);

                            }

                        })->where('status',1)->get();

        $courseDetails = CourseMaster::where('status',1)->get();

		return view('quick-inquiry-form.index', compact('title','request', 'inquires', 'centerDetails','courseDetails'));



	}


	public function create(Request $request) {



		$title = 'Add Quick Inquiry';



		$corses = CourseMaster::where('status',1)->get();



		$centers = CenterMaster::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->where('id',Auth::guard()->user()->branch_id);

                            }

                        })->where('status',1)->get();



		return view('quick-inquiry-form.create', compact('title','corses','centers'));


	}

	public function store(Request $request) {



		// dd($request->all());



		$message = [

			'mr_mrs.required' => 'The field is required.',


			'first_name.required' => 'The field is required.',


			'mobile_no.required' => 'The field is required.',

		];


		$validator = Validator::make($request->all(), [



			'mr_mrs' => 'required',

			'first_name' => 'required',

			'mobile_no' => 'required',



		], $message);







		if ($validator->fails()) {


			return redirect()->back()



				->withErrors($validator)



				->withInput();



		}







		try {



			// dd('in');



			$inquiry_id = QuickInquiryForm::insertGetId([



				'mr_mrs' => $request->mr_mrs,



				'first_name' => $request->first_name,



				'mid_name' => $request->mid_name,



				'last_name' => $request->last_name,



				'date_of_birth' => $request->date_of_birth,



				'occupation' => $request->occupation,



				'address' => $request->address,



				'city' => $request->city,



				'pin_code' => $request->pin_code,



				'telephone_no' => $request->telephone_no,



				'mobile_no' => $request->mobile_no,



				'email' => $request->email,



				'qualification' => $request->qualification,



				'course' => $request->course,



				'center_name' => $request->center_name,



				'status' => $request->status,



				'created_by' => Auth::guard()->user()->id



			]);


			
			// Admin Email

			// $to = "somnath@imagic.net.in,admission@imagic.net.in";

			$to = "girisurajit.07@gmail.com";

			$subject = "Quick Inquiry Form Data";

			$txt = "Name : ".$request->mr_mrs." ".$request->first_name." ".$request->mid_name." ".$request->last_name."\n";

			$txt .= "Phone Number : ".$request->mobile_no."\n";

			$txt .= "Email : ".$request->email."\n";

			if($request->course){

			$txt .= "Course : ".Helper::getCourseName($request->course)."\n";

			}

			if($request->center_name){

			$txt .= "Center Name : ".Helper::getCenterName($request->center_name)."\n";

			}

			$txt .= "Inquey Id : IMAGIC-".$inquiry_id."\n";

			$headers = "From: info@pinealmagic.in";



			// mail($to,$subject,$txt,$headers);



			Session::flash('msg', 'Inquiry sucessfully created');



			return redirect()->route('quick-inquiry-form.index');



		} catch (\Exception $e) {
			dd($e);
			Session::flash('error', $e->getMessage());

			return redirect()->back();

		}

	}

	public function show($id) {

	}

	public function edit(Request $request, $id) {



		$title = 'Quick Inquiry Edit';



		$corses = CourseMaster::where('status',1)->get();



		$centers = CenterMaster::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->where('id',Auth::guard()->user()->branch_id);

                            }

                        })->where('status',1)->get();



		$inquiry = QuickInquiryForm::find($id);



		return view('quick-inquiry-form.edit', compact('title', 'corses','centers','inquiry'));



	}



	public function update(Request $request, $id) {



		$message = [


			'mr_mrs.required' => 'The field is required.',



			'first_name.required' => 'The field is required.',



			'mobile_no.required' => 'The field is required.',



		];







		$validator = Validator::make($request->all(), [



			'mr_mrs' => 'required',



			'first_name' => 'required',


			'mobile_no' => 'required',



		], $message);







		if ($validator->fails()) {



			return redirect()->back()



				->withErrors($validator)



				->withInput();



		}



		 \DB::beginTransaction();



		try {



			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));



			$inquiry_update = QuickInquiryForm::where('id', $id)



				->update([



				'mr_mrs' => $request->mr_mrs,



				'first_name' => $request->first_name,



				'mid_name' => $request->mid_name,



				'last_name' => $request->last_name,



				'date_of_birth' => $request->date_of_birth,



				'occupation' => $request->occupation,



				'address' => $request->address,



				'city' => $request->city,



				'pin_code' => $request->pin_code,



				'telephone_no' => $request->telephone_no,



				'mobile_no' => $request->mobile_no,



				'email' => $request->email,



				'qualification' => $request->qualification,



				'course' => $request->course,



				'center_name' => $request->center_name,



				'status' => $request->status,


				'updated_by' => Auth::guard()->user()->id



			]);

				// dd($inquiry_update);



			\DB::commit();



			Session::flash('msg', 'Inquiry updated sucessfully');



			return redirect()->route('quick-inquiry-form.index');



		} catch (\Exception $e) {



			\Db::rollback();



			Session::flash('error', $e->getMessage());



			return redirect()->back();

		}

	}


	public function destroy($id) {



		try {

			$inquiry = QuickInquiryForm::find($id);

			$inquiry->delete();

			return response()->json(['status' => 200, 'status_text' => 'Inquiry successfuly deleted']);


		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);


		}

	}




}



