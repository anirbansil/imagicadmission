<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
	protected $table = 'roles';

	protected $fillable = ['name', 'slug', 'permissions', 'status'];

	public function users() {
		return $this->belongsToMany(User::class, 'role_users');
	}

	public function hasAccess(array $permissions): bool {
		foreach ($permissions as $permission) {
			if ($this->hasPermission($permission)) {
				return true;
			}

		}
		return false;
	}

	private function hasPermission(string $permission): bool {
		//return $this->permissions[$permission] ?? false;
		return $this->permission_array[$permission] ?? false;
	}

	public function getPermissionArrayAttribute() {
		return json_decode($this->permissions, true);
	}

	public function getStatusTextAttribute() {
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

	public function scopeGetPermissions($query, $permission) {
		//return $permission->pluck('key'); //print_r($permission);
		$data = [];
		foreach ($permission as $key => $value) {
			if ($key == 'all') {
				$data[] = 'All';
			} else {
				$permission = Permission::where('name', $key)->first();
				$data[] = "{$permission->main_label} - {$permission->label}";
			}

		}
		return implode(', ', $data);
	}
	
}
