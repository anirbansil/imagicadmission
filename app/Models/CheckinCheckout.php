<?php

namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CheckinCheckout extends Model
{
	use SoftDeletes;

    protected $table = 'checkin_checkout';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','course','module','date','check_in_time','checkout_time'
    ];
    

    public function courseName(){
        return $this->belongsTo(CourseMaster::class, 'course', 'id' );
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id' );
    }
}
