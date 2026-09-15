<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\SeatBook;

use App\Models\CourseMaster;

use App\Models\CenterMaster;

use App\Exports\InquiryExport;

use Maatwebsite\Excel\Facades\Excel;

use Auth;

use Illuminate\Http\Request;

use Session;

use Validator;

use Carbon\Carbon;

use DB;

use Helper;



use GuzzleHttp\Client;





class SeatBookingController extends Controller {

	public function index(Request $request)

    {

		$title = 'Seat Booking List';



        $inquires = SeatBook::where(function ($q) use ($request) {


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

		return view('seat.index', compact('title','request', 'inquires', 'centerDetails','courseDetails'));



	}


	public function create(Request $request) {



		$title = 'Seat Book';



		$corses = CourseMaster::where('status',1)->get();



		$centers = CenterMaster::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->where('id',Auth::guard()->user()->branch_id);

                            }

                        })->where('status',1)->get();



		return view('seat.create', compact('title','corses','centers'));







	}

	public function store(Request $request) {



		// dd($request->all());



		$message = [



			'in_date.required' => 'The field is required.',



			'serial_no.required' => 'The field is required.',



			'mr_mrs.required' => 'The field is required.',



			'first_name.required' => 'The field is required.',



			// 'date_of_birth.required' => 'The field is required.',



			// 'occupation.required' => 'The field is required.',



			// 'address.required' => 'The field is required.',



			// 'city.required' => 'The field is required.',



			// 'pin_code.required' => 'The field is required.',



			'mobile_no.required' => 'The field is required.',



			// 'email.required' => 'The field is required.',



			// 'qualification.required' => 'The field is required.',



			'know_about_imagic.required' => 'The field is required.'







		];







		$validator = Validator::make($request->all(), [



			'mr_mrs' => 'required',



			'first_name' => 'required',



			// 'date_of_birth' => 'required',



			// 'occupation' => 'required',



			// 'address' => 'required',



			// 'city' => 'required',



			// 'pin_code' => 'required',



			'mobile_no' => 'required',



			// 'email' => 'required',



			// 'qualification' => 'required',



			'know_about_imagic' => 'required',



		], $message);







		if ($validator->fails()) {



			return redirect()->back()



				->withErrors($validator)



				->withInput();



		}







		try {



			// dd('in');



			$inquiry_id = SeatBook::insertGetId([



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



				'know_about_imagic' => $request->know_about_imagic,



				'course' => $request->course,



				'center_name' => $request->center_name,



				'status' => $request->status,



				'general_remarks' => $request->general_remarks,



				'was_show_demo_class' => $request->was_show_demo_class,



				'whom_show_demo_class' => $request->whom_show_demo_class,



				'when_show_demo_class' => $request->when_show_demo_class,



				'date_of_reg' => $request->date_of_reg,



				'created_by' => Auth::guard()->user()->id



			]);







			$inquery_update = SeatBook::where('id', $inquiry_id)



			              ->update(['serial_no' => 'IMAGIC-'.$inquiry_id]);



			// Admin Email

			$to = "somnath@imagic.net.in,admission@imagic.net.in";

			$subject = "Inquiry Form Data";

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



			// Student Email 

			$s_to = $request->email;

			$s_subject = "Inquiry Form Data";

			$s_txt = "Thanks for contacting IMAGIC. We will get back to you soon or you may also call 9163825304.";

			$s_headers = "From: info@pinealmagic.in";



			mail($s_to,$s_subject,$s_txt,$s_headers);



			// SMS

			$url = "https://sms11.infonetservices.in/api/api_http.php";

		    $your_phone = $request->mobile_no;

		    

			  $param = array(

			      'username' => 'imagic',

			      'password' => 'imagic122',

			      'senderid' => 'iMAGIC',

			      'text' => 'Thanks for contacting IMAGIC. We will get back to you soon or you may also call 9163825304.',

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





			Session::flash('msg', 'Seat Book sucessfully created');



			return redirect()->route('seat.index');



		} catch (\Exception $e) {



			// dd($e);



			Session::flash('error', $e->getMessage());







			return redirect()->back();







		}







	}

	public function show($id) {
	}


	public function edit(Request $request, $id) {



		$title = 'Seat Book Edit';



		$corses = CourseMaster::where('status',1)->get();



		$centers = CenterMaster::where(function($q) use ($request) {

                            if(Auth::guard()->user()->is_admin != 1){

                                $q->where('id',Auth::guard()->user()->branch_id);

                            }

                        })->where('status',1)->get();



		$inquiry = SeatBook::find($id);



		return view('seat.edit', compact('title', 'corses','centers','inquiry'));



	}

	public function update(Request $request, $id) {



		$message = [



			'in_date.required' => 'The field is required.',



			'serial_no.required' => 'The field is required.',



			'mr_mrs.required' => 'The field is required.',



			'first_name.required' => 'The field is required.',



			// 'date_of_birth.required' => 'The field is required.',



			// 'occupation.required' => 'The field is required.',



			// 'address.required' => 'The field is required.',



			// 'city.required' => 'The field is required.',



			// 'pin_code.required' => 'The field is required.',



			'mobile_no.required' => 'The field is required.',



			// 'email.required' => 'The field is required.',



			// 'qualification.required' => 'The field is required.',



			'know_about_imagic.required' => 'The field is required.'







		];







		$validator = Validator::make($request->all(), [



			'mr_mrs' => 'required',



			'first_name' => 'required',



			// 'date_of_birth' => 'required',



			// 'occupation' => 'required',



			// 'address' => 'required',



			// 'city' => 'required',



			// 'pin_code' => 'required',



			'mobile_no' => 'required',



			// 'email' => 'required',



			// 'qualification' => 'required',



			'know_about_imagic' => 'required',



		], $message);







		if ($validator->fails()) {



			return redirect()->back()



				->withErrors($validator)



				->withInput();



		}



		 \DB::beginTransaction();



		try {



			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));



			$inquiry_update = SeatBook::where('id', $id)



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



				'know_about_imagic' => $request->know_about_imagic,



				'course' => $request->course,



				'center_name' => $request->center_name,



				'status' => $request->status,



				'general_remarks' => $request->general_remarks,



				'was_show_demo_class' => $request->was_show_demo_class,



				'whom_show_demo_class' => $request->whom_show_demo_class,



				'when_show_demo_class' => $request->when_show_demo_class,



				'date_of_reg' => $request->date_of_reg,



				'updated_by' => Auth::guard()->user()->id



			]);

				// dd($inquiry_update);



			\DB::commit();



			Session::flash('msg', 'Seat Book updated sucessfully');



			return redirect()->route('seat.index');



		} catch (\Exception $e) {



			\Db::rollback();



			Session::flash('error', $e->getMessage());



			return redirect()->back();

		}

	}

	public function destroy($id) {

		try {

			$inquiry = SeatBook::find($id);

			$inquiry->delete();

			return response()->json(['status' => 200, 'status_text' => 'Seat Book successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);
		}

	}


	public function InquiryExport(Request $request) {

		$title = 'Seat Book Report';

		$inquires = SeatBook::where(function ($q) use ($request) {

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



                $q->orWhere('serial_no', 'LIKE', '%' . $request->input('input_search') . '%');

                $q->orWhere('qualification', 'LIKE', '%' . $request->input('input_search') . '%');

            }

            if(Auth::guard()->user()->is_admin != 1){

                $q->where('center_name',Auth::guard()->user()->branch_id);

            }



        })->orderBy('id', 'desc')->get();
		

		$data = [
			'inquires' => $inquires
		];

		return Excel::download(new InquiryExport($data), 'seat-book-reports.xlsx');

	}


}



