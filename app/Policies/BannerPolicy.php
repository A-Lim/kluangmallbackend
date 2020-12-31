<?php

namespace App\Policies;

use App\User;
use App\Banner;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy {
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
     * Determine whether the user can view any banners.
     *
     * @param  \App\Banner $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('banners.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the banner.
     *
     * @param  \App\User $user
     * @param  \App\Banner $banner
     * @return mixed
     */
    public function view(User $user, Banner $banner) {
        return $user->can('banners.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create banners.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user) {
        return $user->can('banners.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the banner.
     *
     * @param  \App\User $user
     * @param  \App\Banner $banner
     * @return mixed
     */
    public function update(User $user, Banner $banner) {
        return $user->can('banners.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the banner.
     *
     * @param  \App\User $user
     * @param  \App\Banner $banner
     * @return mixed
     */
    public function delete(User $user, Banner $banner) {
        return $user->can('banners.delete') && 
            $user->status == 'active';
    }
}
