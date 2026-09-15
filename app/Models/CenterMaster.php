<?php
 
namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
 
class CenterMaster extends Model
{
	use SoftDeletes;
	
    protected $table = 'center_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gst', 'gst_no','sc_per','center_amount','use_amount','center_amount_updated', 'status','created_by','updated_by'
    ];
    

}
