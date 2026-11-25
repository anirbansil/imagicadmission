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
		] + self::locationRules(), $message + self::locationMessages());

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
			self::saveLocation($course_insert, $request);

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

		] + self::locationRules(), $message + self::locationMessages());

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
			self::saveLocation(CenterMaster::find($id), $request);
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



	/**
	 * My Centre: a centre head (Center role) keeps their own centre's address and
	 * check-in location up to date. Admin edits every centre from Centre Master.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function mine() {
		$center = $this->ownCenter();
		if (!$center) {
			Session::flash('error', 'Your account is not linked to a centre. Please ask the admin.');
			return redirect()->route('dashboard');
		}
		$title = 'My Centre';
		return view('center-master.mine', compact('title', 'center'));
	}

	public function updateMine(Request $request) {
		$center = $this->ownCenter();
		if (!$center) {
			return redirect()->route('dashboard');
		}
		$request->validate(self::locationRules(), self::locationMessages());
		self::saveLocation($center, $request);
		Session::flash('msg', 'Centre address, email and check-in location saved.');
		return redirect()->route('center-master.mine');
	}

	/**
	 * The centre of the logged-in centre head (users.branch_id), or null.
	 */
	private function ownCenter() {
		$user = Auth::guard()->user();
		if ($user->is_admin == 1 || !$user->roles->pluck('slug')->contains('center') || !$user->branch_id) {
			return null;
		}
		return CenterMaster::find($user->branch_id);
	}

	public static function locationRules() {
		return [
			'address' => 'nullable|string|max:500',
			'email' => 'nullable|email|max:191',
			'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
			'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
			'geo_radius' => 'nullable|integer|between:50,2000',
		];
	}

	public static function locationMessages() {
		return [
			'latitude.required_with' => 'Enter both latitude and longitude, or leave both empty.',
			'longitude.required_with' => 'Enter both latitude and longitude, or leave both empty.',
			'latitude.between' => 'Latitude must be between -90 and 90 (for example 22.5726459).',
			'longitude.between' => 'Longitude must be between -180 and 180 (for example 88.3638953).',
			'geo_radius.between' => 'The allowed distance must be between 50 and 2000 metres.',
			'email.email' => 'Enter a valid centre email address.',
		];
	}

	/**
	 * Store a centre's address and check-in location; remembers who changed the location and when.
	 */
	public static function saveLocation($center, Request $request) {
		if (!$center) {
			return;
		}
		$lat = is_numeric($request->latitude) ? round((float) $request->latitude, 7) : null;
		$lng = is_numeric($request->longitude) ? round((float) $request->longitude, 7) : null;
		$radius = $request->geo_radius ? (int) $request->geo_radius : null;
		$moved = $lat != $center->latitude || $lng != $center->longitude || $radius != $center->geo_radius;
		$center->address = $request->address !== null ? trim($request->address) : null;
		$center->email = $request->email ? strtolower(trim($request->email)) : null;
		$center->latitude = $lat;
		$center->longitude = $lng;
		$center->geo_radius = $radius;
		if ($moved) {
			$center->location_updated_by = Auth::guard()->user()->id;
			$center->location_updated_at = Carbon::now();
		}
		$center->save();
	}

}
