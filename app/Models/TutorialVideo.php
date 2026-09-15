<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class TutorialVideo extends Model
{
	use SoftDeletes;
	
    protected $table = 'tutorial_video';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','iframe','status','for_which','created_by','updated_by'
    ];
    

}
