<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Planner;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlannerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Planner $planner)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $planner->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->manager->company_id === $planner->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function update(User $user, Planner $planner)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $planner->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->manager->company_id === $planner->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Planner $planner)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $planner->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->manager->company_id === $planner->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }


    public function restore(User $user, Planner $planner)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $planner->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->manager->company_id === $planner->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function forceDelete(User $user, Planner $planner)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $planner->company_id;
        }
        if($user->hasRole('manager')) {
            return $user->manager->company_id === $planner->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }
}
