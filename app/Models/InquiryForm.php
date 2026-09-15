<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class InquiryForm extends Model
{
	use SoftDeletes;
	
    protected $table = 'inquiry_form';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'in_date', 'serial_no','mr_mrs','first_name','mid_name','last_name','date_of_birth','occupation','address','city','pin_code','telephone_no','mobile_no','email','qualification','know_about_imagic','course','center_name','general_remarks','was_show_demo_class','whom_show_demo_class','when_show_demo_class','date_of_reg','status','created_by','updated_by'
    ];
    
    public function regDetails() {
        return $this->hasOne(RegistrationForm::class, 'inquery_id', 'serial_no');
    }

}
