<?php

namespace App\Policies;

use App\Models\Finance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $role = $user->roles()->first()->name;

        if ($role === "admin") {
            return true;
        } else {
            $permission = $user->admissions()->first()->name;
            return $permission === "A4";
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Finance $finance): bool
    {
        $role = $user->roles()->first()->name;

        if ($role === "admin") {
            return true;
        } else {
            $permission = $user->admissions()->first()->name;
            return $permission === "A4";
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["admin", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return $permission === "A4";
        }  
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Finance $finance): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["admin", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return $permission === "A4";
        }   
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Finance $finance): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["admin", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return $permission === "A4";
        }    
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Finance $finance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Finance $finance): bool
    {
        return false;
    }
}
