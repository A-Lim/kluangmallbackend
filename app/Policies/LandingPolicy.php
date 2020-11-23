<?php

namespace App\Policies;

use App\User;
use App\Landing;
use Illuminate\Auth\Access\HandlesAuthorization;

class LandingPolicy {
    use HandlesAuthorization;

    /**
     * Bypass any policy
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function before(User $user, $ability) {
        if ($user->isAdmin())
            return true;
    }

    /**
     * Determine whether the user can view any landings.
     *
     * @param  \App\Landing $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('landings.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can update the landing.
     *
     * @param  \App\User $user
     * @param  \App\Landing $landing
     * @return mixed
     */
    public function update(User $user, Landing $landing) {
        return $user->can('landings.update') && 
            $user->status == 'active';
    }
}
