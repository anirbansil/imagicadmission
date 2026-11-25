<?php

namespace App\Http\Controllers\Traits;

use App\Models\RegistrationForm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Who may manage student attendance (requests, class schedules, holidays):
 * superadmin for every student, center accounts for students registered at their center.
 */
trait ManagesStudentAttendance
{
    protected function canManageAttendance()
    {
        return Auth::guard()->user()->is_admin == 1 || $this->isCenter();
    }

    protected function isCenter()
    {
        $user = Auth::guard()->user();
        return $user->is_admin != 1 && $user->roles->pluck('slug')->contains('center');
    }

    /**
     * User ids of the students registered at a center.
     */
    protected function centerStudentIds($centerId)
    {
        $emails = RegistrationForm::where('center_name', $centerId)->pluck('email');
        return User::whereIn('email', $emails)->pluck('id');
    }

    /**
     * Limit a query with a user_id column to the logged-in center's students.
     */
    protected function scopeToCenter($query, $column = 'user_id')
    {
        if ($this->isCenter()) {
            $query->whereIn($column, $this->centerStudentIds(Auth::guard()->user()->branch_id));
        }
        return $query;
    }
}
