<?php

namespace App\Policies;

use App\User;
use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy {
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
     * Determine whether the user can view any merchants.
     *
     * @param  \App\Merchant $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('merchants.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the merchant.
     *
     * @param  \App\User $user
     * @param  \App\Merchant $merchant
     * @return mixed
     */
    public function view(User $user, Merchant $merchant) {
        return $user->can('merchants.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create merchants.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(Merchant $merchant) {
        return $user->can('merchants.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the merchant.
     *
     * @param  \App\User $user
     * @param  \App\Merchant $merchant
     * @return mixed
     */
    public function update(User $user, Merchant $merchant) {
        return $user->can('merchants.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the merchant.
     *
     * @param  \App\User $user
     * @param  \App\Merchant $merchant
     * @return mixed
     */
    public function delete(User $user, Merchant $merchant) {
        return $user->can('merchants.delete') && 
            $user->status == 'active';
    }
}
