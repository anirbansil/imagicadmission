<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TutorialVideo;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use Helper;

class TutorialVideoController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function index(Request $request) {

		$title = 'Video List';

		$categories = TutorialVideo::orderBy('id', 'desc')->paginate(10);

		return view('video-master.index', compact('title', 'categories'));

	}

	/**

	 * Show the form for creating a new resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function create() {
		$title = 'Add Video';

		return view('video-master.create', compact('title'));

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
			'title.required' => 'The title field is required.',
			'iframe.required' => 'The iframe field is required.',
			'status.required' => 'The status field is required.',
			'for_which.required' => 'This field is required.'
		];

		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'iframe' => 'required',
			'status' => 'required',
			'for_which' => 'required'
		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$course_insert = TutorialVideo::create([
				'title' => $request->title,
				'iframe' => $request->iframe,
				'status' => $request->status,
				'for_which' => $request->for_which,
				'created_by' => Auth::guard()->user()->id
			]);

			//dd($course_insert);
			Session::flash('msg', 'Video sucessfully created');
			return redirect()->route('video-master.index');
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

	public function show() {

		$admin = Auth::guard()->user();

		$roleArray = $admin->roles()->pluck('slug')->toArray();

		$title = 'Videos';

		if($roleArray[0] == 'student'){
        	$for_which = 1;
        }elseif($roleArray[0] == 'outsider_student'){
        	$for_which = 3;
        }else{
        	$for_which = 2;
        }

		$categories = TutorialVideo::where('for_which',$for_which)->orderBy('id', 'desc')->where('status',1)->paginate(10);

		return view('video-master.show', compact('title', 'categories'));

	}

	/**

	 * Show the form for editing the specified resource.

	 *

	 * @param  int  $id

	 * @return \Illuminate\Http\Response

	 */

	public function edit($id) {
		$title = 'Video Edit';
		$category = TutorialVideo::find($id);
		return view('video-master.edit', compact('title', 'category'));
	}



	public function update(Request $request, $id) {
		//echo $id;die;
		// dd($request->number_of_emi);
		$message = [
			'title.required' => 'The title field is required.',
			'iframe.required' => 'The iframe field is required.',
			'status.required' => 'The status field is required.',
			'for_which.required' => 'This field is required.'
		];

		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'iframe' => 'required',
			'status' => 'required',
			'for_which' => 'required'
		], $message);


		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
		 \DB::beginTransaction();
		try {
			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
			$course_update = TutorialVideo::where('id', $id)
				->update([
				'title' => $request->title,
				'iframe' => $request->iframe,
				'status' => $request->status,
				'for_which' => $request->for_which,
				'updated_by' => Auth::guard()->user()->id
			]);
			\DB::commit();
			Session::flash('msg', 'Video updated sucessfully');
			return redirect()->route('video-master.index');
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

			$category = TutorialVideo::find($id);

			$category->delete();

			return response()->json(['status' => 200, 'status_text' => 'Video successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}



}
