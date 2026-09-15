<?php



namespace App;

namespace App\Models;



use Illuminate\Database\Eloquent\Model;



use Illuminate\Database\Eloquent\SoftDeletes;



class CourseSubModule extends Model

{

	use SoftDeletes;



    protected $table = 'sub_modules';



    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'course_id', 'module_id', 'sub_module_name',

    ];





}

