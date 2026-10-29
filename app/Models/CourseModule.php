<?php

namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModule extends Model
{
	use SoftDeletes;

    protected $table = 'module_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'module_name', 'date', 'created_at', 'updated_at'
    ];


}
