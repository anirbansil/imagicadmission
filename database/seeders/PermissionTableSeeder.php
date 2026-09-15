<?php

namespace Database\Seeders;

use App\Models\Permission;

use Illuminate\Database\Seeder;



class PermissionTableSeeder extends Seeder {

	/**

	 * Run the database seeds.

	 *

	 * @return void

	 */

	public $permissionArray = [

		['name' => 'user-list', 'label' => 'List', 'model_name' => 'users', 'main_label' => 'Users'],

		['name' => 'user-create', 'label' => 'Create', 'model_name' => 'users', 'main_label' => 'Users'],

		['name' => 'user-edit', 'label' => 'Edit', 'model_name' => 'users', 'main_label' => 'Users'],

		['name' => 'user-delete', 'label' => 'Delete', 'model_name' => 'users', 'main_label' => 'Users'],

		['name' => 'role-list', 'label' => 'List', 'model_name' => 'roles', 'main_label' => 'Roles'],

		['name' => 'role-create', 'label' => 'Create', 'model_name' => 'roles', 'main_label' => 'Roles'],

		['name' => 'role-edit', 'label' => 'Edit', 'model_name' => 'roles', 'main_label' => 'Roles'],

		['name' => 'role-delete', 'label' => 'Delete', 'model_name' => 'roles', 'main_label' => 'Roles'],

		['name' => 'course', 'label' => 'Course Master', 'model_name' => 'course_master', 'main_label' => 'Course Master'],

		['name' => 'inquery', 'label' => 'Inquery Form', 'model_name' => 'inquiry_form', 'main_label' => 'Inquery Form'],

		['name' => 'registration', 'label' => 'Registration Form', 'model_name' => 'registration_form', 'main_label' => 'Registration Form'],

		['name' => 'application', 'label' => 'Application Form', 'model_name' => 'application_form', 'main_label' => 'Application Form'],

		['name' => 'receipt', 'label' => 'Receipt', 'model_name' => 'receipt', 'main_label' => 'Receipt'],

		['name' => 'report', 'label' => 'Reports', 'model_name' => 'report', 'main_label' => 'Reports'],

		

	];



	public function run() {

		foreach ($this->permissionArray as $key => $value) {

			Permission::create($value);

		}



	}

}

