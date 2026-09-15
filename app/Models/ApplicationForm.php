<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class ApplicationForm extends Model
{
	use SoftDeletes;
	
    protected $table = 'application_form';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reg_id', 'application_id', 'course','batch_start_date','course_fee','pay_on_admission','batch_end_date','scholarship','balance','batch_time','timeunit','admission_fee','number_of_emi','course_by_word','gst_amount','net_total','emi','emi_date','faculty_id','remarks','paid_by','cheque_no','cheque_date','cheque_dwawn','online_pay_number','job_required','created_by','updated_by'
    ];

    public function regDetails() {
        return $this->hasOne(RegistrationForm::class, 'id', 'reg_id');
    }

    public function appEmi() {
        return $this->hasMany(ApplicationFormEmi::class, 'application_id', 'id');
    }
    
    public function applicationEmi() {
        return $this->hasMany(ApplicationFormEmi::class, 'application_id', 'id');
    }

    
}
