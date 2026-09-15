<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FacultyMaster;
use App\Models\FacultySubject;
use App\Models\CourseMaster;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;

class FacultyMasterController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function index(Request $request) {

		$title = 'Faculty List';

		$faculties = FacultyMaster::where(function ($q) use ($request) {
										if(Auth::guard()->user()->is_admin != 1){
							                $q->where('created_by',Auth::guard()->user()->id);
							            }
									})->orderBy('id', 'desc')->paginate(10);

		return view('faculty-master.index', compact('title', 'faculties'));

	}

	/**

	 * Show the form for creating a new resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function create() {
		$title = 'Add Faculty';

		$courses = CourseMaster::where('status',1)->get();

		return view('faculty-master.create', compact('title','courses'));

	}

	/**

	 * Store a newly created resource in storage.

	 *

	 * @param  \Illuminate\Http\Request  $request

	 * @return \Illuminate\Http\Response

	 */

	public function store(Request $request) {
		//dd($request->all());
		$message = [
			'surname.required' => 'This field is required.',
			'name.required' => 'This field is required.',
			'email.required' => 'This field is required.',
			'phone_no.required' => 'This field is required.'
		];

		$validator = Validator::make($request->all(), [
			'surname' => 'required',
			'name' => 'required',
			'email' => 'required',
			'phone_no' => 'required'
		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$insert = FacultyMaster::create([
				'surname' => $request->surname,
				'name' => $request->name,
				'email' => $request->email,
				'phone_no' => $request->phone_no,
				'subject' => '',
				'created_by' => Auth::guard()->user()->id
			]);

			if(count($request->subject) > 0){
				for($j = 0; $j < count($request->subject); $j++){				

					$faculty_subject = FacultySubject::insert([

						'faculty_id' => $insert->id,

						'subject' => $request->subject[$j]]);
				}
			}

			//dd($course_insert);
			Session::flash('msg', 'Faculty sucessfully created');
			return redirect()->route('faculty-master.index');
		} catch (\Exception $e) {
			dd($e);
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
		$title = 'Faculty Edit';
		$faculty = FacultyMaster::find($id);
		$courses = CourseMaster::where('status',1)->get();
		return view('faculty-master.edit', compact('title', 'faculty','courses'));
	}



	public function update(Request $request, $id) {
		//echo $id;die;
		$message = [
			'surname.required' => 'This field is required.',
			'name.required' => 'This field is required.',
			'email.required' => 'This field is required.',
			'phone_no.required' => 'This field is required.'
		];

		$validator = Validator::make($request->all(), [
			'surname' => 'required',
			'name' => 'required',
			'email' => 'required',
			'phone_no' => 'required'
		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
		 \DB::beginTransaction();
		try {
			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
			$faculty_update = FacultyMaster::where('id', $id)
				->update([
				'surname' => $request->surname,
				'name' => $request->name,
				'email' => $request->email,
				'phone_no' => $request->phone_no,
				'subject' => '',
				'updated_by' => Auth::guard()->user()->id
			]);

			$exist_ins = FacultySubject::where('faculty_id', $id)->get()->pluck('id')->toArray();

			if(!empty($request->subject)){
				$unmatchedins = array_diff($exist_ins, $request->subject);
				for($j = 0; $j < count($request->subject); $j++){
					$inspection_register_of_the_religious = FacultySubject::updateOrCreate(["id"=>$request->subject[$j],"faculty_id"=>$id],[

							'faculty_id' => $id,

							'subject' => $request->subject[$j]

						]);
				}

				if(!empty($unmatchedins)){
					FacultySubject::where('faculty_id',$id)->whereIn('id',$unmatchedins)->delete();
				}
			}

			\DB::commit();
			Session::flash('msg', 'Faculty updated sucessfully');
			return redirect()->route('faculty-master.index');
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

			$category = FacultyMaster::find($id);

			$category->delete();

			return response()->json(['status' => 200, 'status_text' => 'Course successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}



}
