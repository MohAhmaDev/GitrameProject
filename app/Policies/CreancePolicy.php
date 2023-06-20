<?php

namespace App\Policies;

use App\Models\Creance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CreancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $role = $user->roles()->first()->name;

        if ($role === "global") {
            return true;
        } else {
            $permission = $user->admissions()->first()->name;
            return in_array($permission, ["A3", "A5"]);
        }    
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Creance $creance): bool
    {
        $role = $user->roles()->first()->name;

        if ($role === "global") {
            return true;
        } else {
            $permission = $user->admissions()->first()->name;
            return in_array($permission, ["A3", "A5"]);
        }    
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["global", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return in_array($permission, ["A3", "A5"]);
        } 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Creance $creance): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["global", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return in_array($permission, ["A3", "A5"]);
        } 
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Creance $creance): bool
    {
        $role = $user->roles()->first()->name;
        $auth = ["global", "basic"];

        if (in_array($role, $auth)) {
            return false;
        } else {
            $permission = $user->admissions()->first()->name;
            return in_array($permission, ["A3", "A5"]);
        } 
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Creance $creance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Creance $creance): bool
    {
        return false;
    }
}
