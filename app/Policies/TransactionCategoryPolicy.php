<?php

namespace App\Policies;

use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransactionCategory $transactionCategory): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->admin->company_id === $transactionCategory->company_id;
        }

        if ($user->hasRole('manager')) {
            return $user->manager->company_id === $transactionCategory->company_id;
        }

        if ($user->hasRole('planner')) {
            return $user->planner->company_id === $transactionCategory->company_id;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransactionCategory $transactionCategory): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->admin->company_id === $transactionCategory->company_id;
        }

        if ($user->hasRole('manager')) {
            return $user->manager->company_id === $transactionCategory->company_id;
        }

        if ($user->hasRole('planner')) {
            return $user->planner->company_id === $transactionCategory->company_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransactionCategory $transactionCategory): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if($user->hasRole('admin')){
            return $user->admin->company_id === $transactionCategory->company_id;
        }

        if($user->hasRole('manager')){
            return $user->manager->company_id === $transactionCategory->company_id;
        }

        if($user->hasRole('planner')){
            return $user->planner->company_id === $transactionCategory->company_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TransactionCategory $transactionCategory): bool
    {
        return $user->hasRole('admin') && $user->company_id === $transactionCategory->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TransactionCategory $transactionCategory): bool
    {
        return $user->hasRole('admin') && $user->company_id === $transactionCategory->company_id;
    }
}
