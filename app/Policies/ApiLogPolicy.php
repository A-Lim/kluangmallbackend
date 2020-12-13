<?php

namespace App\Policies;

use App\User;
use App\ApiLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiLogPolicy {
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
     * Determine whether the user can view any apilogs.
     *
     * @param  \App\ApiLog $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('apilogs.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }
}
