<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use App\Models\CourseMaster;

use App\Models\CenterMaster;

use App\Models\RegistrationForm;

use App\Models\ApplicationForm;

use App\Models\ApplicationFormEmi;

use App\Models\FacultyMaster;

use App\Models\StudentMarks;

use App\Models\User;

use Image;



class Helper {



	public static function sidenavAccess($permission) {

		$admin = Auth::guard()->user();

		if ($admin->is_admin == 1) {

			return true;

		} elseif ($admin->is_admin == 2) {

			if (!$admin->hasAccess([$permission])) {

				return false;

			}

			return true;

		}

	}

	// True only for accounts whose roles are purely student ones (never for admins or staff
	// who also hold another role). Outsider-only accounts are not included.
	public static function isStudent($user = null) {
		$user = $user ?: Auth::guard()->user();
		if (!$user || $user->is_admin == 1) {
			return false;
		}
		$roles = $user->roles->pluck('slug');
		return $roles->contains('student') && $roles->diff(['student', 'outsider_student'])->isEmpty();
	}



	public static function checkIsReviewer() {

		$admin = Auth::guard()->user();

		$roleArray = $admin->roles()->pluck('slug')->toArray();

		if (in_array('adjudicator', $roleArray)) {

			return $admin->facultyPayment()->checkRenumeration($admin);

		} else {

			return false;

		}

	}	

	public static function getCourseName($id){
		$course = CourseMaster::find($id);
		return @$course->name;
	}

	public static function getUser($id){
		$user = User::find($id);
		return @$user;
	}


	public static function getCenterName($id){
		$center = CenterMaster::find($id);
		return @$center->name;
	}

	public static function getRegDetails($id){
		$reg = RegistrationForm::find($id);
		return $reg;
	}

	public static function getRegPhoneNumberByEmiId($id){
		$emi_details = ApplicationFormEmi::find($id);
		$app_details = ApplicationForm::find($emi_details->application_id);
		$reg_details = RegistrationForm::find($app_details->reg_id);
		return $reg_details;
	}

	public static function getApplicationDetails($id){
		$app_details = ApplicationForm::find($id);
		return $app_details;
	}

	public static function getFaculty($id){
		$faculty = FacultyMaster::find($id);
		return $faculty;
	}

	public static function getFacultyApp($id){
		$faculty = FacultyMaster::where('id',$id)->where('created_by',Auth::guard()->user()->id)->first();
		return $faculty;
	}

	public static function marksBymodule($stdn_id,$module){
		$marks = StudentMarks::where('stdnt_id',$stdn_id)->where('module_name',$module)->first();
	 	return $marks;
	}

	public static function getApplicationIdByEmiId($email){
      	// Latest Code 16.09.2026
        /*$user = User::where('email', $email)->first();
        $roleSlugs = $user ? $user->roles()->pluck('slug')->toArray() : [];

        if (!array_intersect($roleSlugs, ['student', 'outsider_student'])) {
            return [];
        }

        $reg_details = RegistrationForm::where('email',$email)->orderBy('id','DESC')->first();

        if(isset($reg_details->id)){
            $app_details = ApplicationForm::where('reg_id',$reg_details->id)->first();
        }else{
            $app_details = [];
        }

        return $app_details;*/
      
		// Old code of 16.09.2026
		$reg_details = RegistrationForm::where('email',$email)->orderBy('id','DESC')->first();

		if(isset($reg_details->id)){
			$app_details = ApplicationForm::where('reg_id',$reg_details->id)->first();
		}else{
			$app_details = [];
		}
		
		return $app_details;
	}

}

