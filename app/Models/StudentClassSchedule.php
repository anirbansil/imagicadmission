<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassSchedule extends Model
{
    protected $table = 'student_class_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','day_of_week','start_time','end_time','effective_from','effective_to','created_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id' );
    }
}
