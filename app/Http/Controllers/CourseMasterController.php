<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CourseMaster;
use App\Models\CourseModule;
use App\Models\CourseSubModule;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use DB;

class CourseMasterController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function index(Request $request) {

		$title = 'Courses List';

		$categories = CourseMaster::orderBy('id', 'desc')->paginate(10);
		// $categories = CourseMaster::orderBy('id', 'desc')->get();
		// $categorie_module =[];
		// foreach ($categories as $categorie) {
		// 	if($categorie->module != NULL){
		// 		$categorie_module = explode(',',$categorie->module);
		// 		foreach($categorie_module as $module){
		// 			$query_result = CourseModule::create([
		// 				'course_id'=>$categorie->id,
		// 				'module_name'=>$module,
		// 			]);
		// 		}
		// 	}
		// }
		return view('course-master.index', compact('title', 'categories'));

	}

	/**

	 * Show the form for creating a new resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function create() {
		$title = 'Add Course';

		return view('course-master.create', compact('title'));

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
			'name.required' => 'The category name field is required.',
			'duration.required' => 'The category duration field is required.',
			'fees.required' => 'The category fees field is required.',
			'gst.required' => 'The category fees field is required.'
		];

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'duration' => 'required',
			'fees' => 'required',
			'gst' => 'required'
		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$module = implode(',',$request->module);
			$course_insert = CourseMaster::create([
				'name' => $request->name,
				'duration' => $request->duration,
				'fees' => $request->fees,
				'number_of_emi' => $request->number_of_emi,
				'gst' => $request->gst,
				'status' => $request->status,
				'module' => $module,
				'created_by' => Auth::guard()->user()->id
			]);
			for($i=0; $i<count($request->module); $i++){
				CourseModule::create([
					'course_id'=>$course_insert->id,
					'module_name'=>$request->module[$i],
					'date'=>$request->module_date[$i],
				]);
			}
			//dd($course_insert);
			Session::flash('msg', 'Course sucessfully created');
			return redirect()->route('course-master.index');
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
		$title = 'Course Edit';
		$category = CourseMaster::find($id);
		return view('course-master.edit', compact('title', 'category'));
	}



	public function update(Request $request, $id) {
		//echo $id;die;
		// dd($request->number_of_emi);
		$message = [
			'name.required' => 'The category name field is required.',
			'duration.required' => 'The category duration field is required.',
			'fees.required' => 'The category fees field is required.',
			'gst.required' => 'The category fees field is required.',
			// 'number_of_emi.gt' => 'Maximum number of EMI is 12'
		];

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'duration' => 'required',
			'fees' => 'required',
			'gst' => 'required',
			// 'number_of_emi' => 'gt:12'

		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
		 \DB::beginTransaction();
		try {
			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
			$course_update = CourseMaster::where('id', $id)
				->update([
				'name' => $request->name,
				'duration' => $request->duration,
				'fees' => $request->fees,
				'number_of_emi' => $request->number_of_emi,
				'gst' => $request->gst,
				'status' => $request->status,
				'module' => $request->module,
				'updated_by' => Auth::guard()->user()->id
			]);
			\DB::commit();
			Session::flash('msg', 'Course updated sucessfully');
			return redirect()->route('course-master.index');
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

			$category = CourseMaster::find($id);

			$category->delete();

			return response()->json(['status' => 200, 'status_text' => 'Course successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}

	public function addSubmodule($id) {
		
		$title = 'Course Sub Module Add';
		$category = CourseMaster::find($id);
		$modules = CourseModule::where(['course_id'=>$id])->get();
		$sub_modules = CourseSubModule::where(['course_id'=>$id])->get();
		return view('course-master.sub-module', compact('title','category','modules','sub_modules'));
		
	}

	public function StoreSubModule(Request $request,$id) {
		try {
		$submodules = CourseSubModule::where(['course_id'=>$id])->delete();
		\DB::beginTransaction();
		for($i=0; $i<count($request->module_name); $i++ ){
			$store = CourseSubModule::create(['course_id'=>$id,'module_id'=>$request->module_name[$i],'sub_module_name'=>$request->sub_module_name[$i]]);
		}
		\DB::commit();
		Session::flash('msg', 'Course Sub Modules Added  sucessfully');
		return redirect()->route('course-master.index');
		} catch (\Exception $e) {
			\DB::rollback();
			Session::flash('error', $e->getMessage());
			return redirect()->back();

		}
	}

	



}
