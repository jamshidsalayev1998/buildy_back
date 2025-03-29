<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
class EmployeePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Employee $employee)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $employee->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->employee->company_id === $employee->company_id && in_array($employee->position, ['employee', 'planner']);
        }
        if($user->hasRole('planner')) {
            return $user->employee->company_id === $employee->company_id && $employee->position === 'employee';
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function update(User $user, Employee $employee)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $employee->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Employee $employee)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $employee->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        if($user->id != $employee->created_by) {
            return false;
        }
        return false;
    }

    public function restore(User $user, Employee $employee)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $employee->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function forceDelete(User $user, Employee $employee)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $employee->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function create(User $user, $position)
    {
        Log::info('Creating employee', [
            'position' => $position,
            'user_role' => $user->roles->pluck('name'),
            'user_id' => $user->id
        ]);

        // Superadmin har qanday xodim yaratishi mumkin
        if($user->hasRole('superadmin')) {
            return true;
        }

        // Admin har qanday xodim yaratishi mumkin
        if($user->hasRole('admin')) {
            return true;
        }

        // Position parametri bo'lmaganda ruxsat berilmaydi
        if(!$position) {
            return false;
        }

        // Position parametri string bo'lishi kerak
        if(!is_string($position)) {
            return false;
        }

        // Position qiymatlarini tekshirish
        $allowedPositions = ['manager', 'planner', 'employee'];
        if(!in_array($position, $allowedPositions)) {
            return false;
        }

        // Manager faqat planner va employee yaratishi mumkin
        if($user->hasRole('manager')) {
            return in_array($position, ['planner', 'employee']);
        }

        // Planner faqat employee yaratishi mumkin
        if($user->hasRole('planner')) {
            return $position === 'employee';
        }

        return false;
    }
}
