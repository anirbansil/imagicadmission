<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceHoliday extends Model
{
    protected $table = 'attendance_holidays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'center_id','date','title','created_by'
    ];
}
