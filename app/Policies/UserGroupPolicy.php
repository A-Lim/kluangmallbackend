<?php

namespace App\Policies;

use App\User;
use App\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy {
    use HandlesAuthorization;

    /**
     * Bypass any policy
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function before(User $user, $ability) {
        if ($user->isAdmin())
            return true;
    }

    /**
     * Determine whether the user can view any user groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('usergroups.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the user group.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function view(User $user, UserGroup $userGroup) {
        return $user->can('usergroups.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create user groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user) {
        return $user->can('usergroups.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the user group.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function update(User $user, UserGroup $userGroup) {
        return $user->can('usergroups.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the user group.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function delete(User $user, UserGroup $userGroup) {
        return $user->can('usergroups.delete') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can restore the user group.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function restore(User $user, UserGroup $userGroup) {
        //
    }

    /**
     * Determine whether the user can permanently delete the user group.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function forceDelete(User $user, UserGroup $userGroup) {
        //
    }
}
