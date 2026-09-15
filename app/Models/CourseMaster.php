<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class CourseMaster extends Model
{
	use SoftDeletes;
	
    protected $table = 'course_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','duration','fees', 'number_of_emi','gst','status','created_by','updated_by'
    ];
    
    public function courseModule() { 
        return $this->hasmany(CourseModule::class, 'course_id', 'id');
    }
}
