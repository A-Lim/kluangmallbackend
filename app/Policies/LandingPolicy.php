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
    public function userViewAny(User $user) {
        return $user->can('userlanding.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can update the landing.
     *
     * @param  \App\User $user
     * @param  \App\Landing $landing
     * @return mixed
     */
    public function userUpdate(User $user, Landing $landing) {
        return $user->can('userlanding.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can view any landings.
     *
     * @param  \App\Landing $user
     * @return mixed
     */
    public function merchantViewAny(User $user) {
        return $user->can('merchant.landing.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can update the landing.
     *
     * @param  \App\User $user
     * @param  \App\Landing $landing
     * @return mixed
     */
    public function merchantUpdate(User $user, Landing $landing) {
        return $user->can('merchant.landing.update') && 
            $user->status == 'active';
    }
}
