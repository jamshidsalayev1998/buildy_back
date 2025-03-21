<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Manager;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManagerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Manager $manager)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $manager->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function update(User $user, Manager $manager)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $manager->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Manager $manager)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $manager->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function create(User $user)
    {
        if($user->hasRole('admin')) {
            return true;
        }
        return false;
    }

    public function restore(User $user, Manager $manager)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $manager->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }

    public function forceDelete(User $user, Manager $manager)
    {
        if($user->hasRole('admin')) {
            return $user->admin->company_id === $manager->company_id;
        }
        if($user->hasRole('superadmin')) {
            return true;
        }
        return false;
    }
}
