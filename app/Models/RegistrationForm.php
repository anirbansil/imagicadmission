<?php

namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationForm extends Model
{
	use SoftDeletes;

    protected $table = 'registration_form';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'roll_no', 'center_name','mr_mrs','first_name','mid_name','last_name','address','res_address','state','city','district','pin_code','date_of_birth','mobile_no','email','qualification','student_photo','id_proff','qualification_certificate','guardian_name','guardian_mob_no','inquery_id','created_by','updated_by'
    ];

    public function courseName(){
        return $this->belongsTo(InquiryForm::class, 'inquery_id', 'serial_no' );
    }

    public function appDetails() {
        return $this->hasOne(ApplicationForm::class, 'reg_id', 'id');
    }
    public function centerDetails() {
        return $this->hasOne(CenterMaster::class, 'id', 'center_name');
    }

    
}
