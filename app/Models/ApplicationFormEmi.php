<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class ApplicationFormEmi extends Model
{
	use SoftDeletes;
	
    protected $table = 'application_form_emi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id','emi_no','emi_dates','payment_type','cheque_no','emi_amounts','extra_amount','created_by','updated_by'
    ];
    
    public function application() { 
        return $this->hasOne(ApplicationForm::class, 'id', 'application_id');
    }

    

    
}
