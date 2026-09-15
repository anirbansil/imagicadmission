<?php

namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMarks extends Model
{
	use SoftDeletes;

    protected $table = 'student_marks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stdnt_id','course_name', 'module_name','sub_module_name','marks','practical_marks','total_marks','created_by'
    ];

    public function student(){
        return $this->belongsTo(ApplicationForm::class, 'stdnt_id', 'id');
    }

    public function courseName(){
        return $this->belongsTo(CourseMaster::class, 'course_name', 'id' );
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'id' );
    }
}
