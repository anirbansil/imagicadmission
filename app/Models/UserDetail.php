<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
  use Notifiable;
	// use SoftDeletes;

	protected $table = 'user_details';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id','first_name', 'last_name', 'address','profile_image', 'branch_name'
	];

	public function branch() {

        return $this->hasOne(BranchMaster::class, 'id', 'branch_name');

    }

    public function customer() {

        return $this->hasOne(Customer::class, 'c_id', 'branch_name');

    }

    public function project() {

        return $this->hasOne(Customer::class, 'c_id', 'branch_name');

    }
} 
