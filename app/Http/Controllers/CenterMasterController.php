<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CenterMaster;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use Carbon\Carbon;

class CenterMasterController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function index(Request $request) {

		$title = 'Centre List';

		$categories = CenterMaster::orderBy('id', 'desc')->paginate(10);

		return view('center-master.index', compact('title', 'categories'));

	}

	/**

	 * Show the form for creating a new resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function create() {
		$title = 'Add Centre';

		return view('center-master.create', compact('title'));

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
			'name.required' => 'The centre name field is required.',
			'gst.required' => 'The gst field is required.',
			'status.required' => 'The status field is required.'
		];

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'gst' => 'required',
			'status' => 'required'
		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$course_insert = CenterMaster::create([
				'name' => $request->name,
				'gst' => $request->gst,
				'gst_no' => $request->gst_no,
				'sc_per' => $request->sc_per,
				'center_amount' => 0,
				'original_amount' => $request->original_amount,
				'center_amount_updated' => $request->center_amount_updated,
				'status' => $request->status,
				'created_by' => Auth::guard()->user()->id
			]);

			//dd($course_insert);
			Session::flash('msg', 'Centre sucessfully created');
			return redirect()->route('center-master.index');
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
		$title = 'Centre Edit';
		$category = CenterMaster::find($id);
		return view('center-master.edit', compact('title', 'category'));
	}



	public function update(Request $request, $id) {
		//echo $id;die;
		// dd($request->number_of_emi);
		$message = [
			'name.required' => 'The name field is required.',
			'gst.required' => 'The gst field is required.',
			'status.required' => 'The status field is required.'
			// 'number_of_emi.gt' => 'Maximum number of EMI is 12'
		];

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'gst' => 'required',
			'status' => 'required'

		], $message);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}
		 \DB::beginTransaction();
		try {
			//$expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
			$course_update = CenterMaster::where('id', $id)
				->update([
				'name' => $request->name,
				'gst' => $request->gst,
				'gst_no' => $request->gst_no,
				'sc_per' => $request->sc_per,
				'center_amount' => $request->center_amount,
				'use_amount' => $request->center_amount != $request->center_amount_old ? 0 : $request->use_amount,
				'center_amount_updated' => $request->center_amount_updated,
				'status' => $request->status,
				'updated_by' => Auth::guard()->user()->id
			]);
			\DB::commit();
			Session::flash('msg', 'Centre updated sucessfully');
			return redirect()->route('center-master.index');
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

			$category = CenterMaster::find($id);

			$category->delete();

			return response()->json(['status' => 200, 'status_text' => 'Centre successfuly deleted']);

		} catch (\Exception $e) {

			\DB::rollback();

			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}



}
