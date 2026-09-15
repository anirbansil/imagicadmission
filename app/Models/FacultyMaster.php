<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class FacultyMaster extends Model
{
	use SoftDeletes;
	
    protected $table = 'faculty_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surname','name','email','phone_no','subject','created_by','updated_by'
    ];

    public function facultySubjects() {

        return $this->hasMany(FacultySubject::class, 'faculty_id', 'id');

    }
    

}
