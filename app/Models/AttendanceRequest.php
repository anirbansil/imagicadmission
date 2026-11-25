<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $table = 'attendance_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','type','from_date','to_date','check_in_time','checkout_time','reason','status','reviewed_by','reviewed_at','admin_note'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id' );
    }

    public function reviewer(){
        return $this->belongsTo(User::class, 'reviewed_by', 'id' );
    }
}
