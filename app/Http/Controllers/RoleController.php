<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Session;
use Validator;
use Hashids\Hashids;

class RoleController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$title = 'Admin | Roles';
		$roles = Role::orderBy('id', 'asc')->paginate(10);
		return view('roles.index', compact('title', 'roles'));
	}
 
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$title = 'Admin | Role Create';
		$permissions = Permission::get()->groupBy('model_name');
		return view('roles.create', compact('title', 'permissions'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		//dd($request->all());
		
		$messsages = array(
			'name.required' => 'The Role Name field is required.',
			'slug.required' => 'The Role Slug field is required.',
			'permissions.required' => 'Checked minimum one permision.',
		);

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'slug' => 'required',
			'permissions' => 'required',
		], $messsages);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		$permissionArray = [];
		if ($request->has('permissions')) {
			foreach ($request->input('permissions') as $value) {
				$permission = Permission::find($value);
				$permissionArray[$permission->name] = true;
			}
		}
	
		try {
			Role::create($request->except('permissions') + ['permissions' => $request->has('permissions') ? json_encode($permissionArray) : '']);
			Session::flash('msg', 'Successfully created');
			return redirect()->route('manage-role.index');
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
		$title = 'Admin | Role Edit';	
		
		$id = decrypt($id);
		$permissions = Permission::get()->groupBy('model_name');
		$role = Role::find($id);
		$setpermissionArray = [];
		$data = $permissions->pluck('name')->toArray();
		if ($role->slug == 'superadmin') {
			foreach ($data as $key => $value) {
				$setpermissionArray[$value] = true;
			}
		} else {
			$setpermissionArray = $role->permission_array;
		}
		//dd($setpermissionArray);

		return view('roles.edit', compact('title', 'role', 'permissions', 'setpermissionArray'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {		

		$messsages = array(
			'name.required' => 'The Role Name field is required.',
			'slug.required' => 'The Role Slug field is required.',
			'permissions.required' => 'Checked minimum one permision.',
		);

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'slug' => 'required',
			'permissions' => 'required',
		], $messsages);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			$permissionArray = [];
			if ($request->has('permissions')) {
				foreach ($request->input('permissions') as $value) {
					$permission = Permission::find($value);
					$permissionArray[$permission->name] = true;
				}
			}
			$role = Role::find($id);
			$role->fill($request->except('permissions') + ['permissions' => $request->has('permissions') ? json_encode($permissionArray) : '']);
			$role->save();
			Session::flash('msg', 'Role data Successfully updated');
			return redirect()->route('manage-role.index');
		} catch (\Exception $e) {
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
			$role = Role::find($id);
			$role->delete();
			return response()->json(['status' => 200, 'status_text' => 'Successfully deleted']);
		} catch (\Exception $e) {
			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);
		}
	}
}
