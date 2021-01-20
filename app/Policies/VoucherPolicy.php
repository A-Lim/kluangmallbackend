<?php

namespace App\Policies;

use App\User;
use App\Voucher;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoucherPolicy {
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
     * Determine whether the user can view any vouchers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('vouchers.view') && 
            ($user->status == 'active' || 
            $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the voucher.
     *
     * @param  \App\User  $user
     * @param  \App\Voucher  $voucher
     * @return mixed
     */
    public function view(User $user, Voucher $voucher) {
        return $user->can('vouchers.view') && 
            ($user->status == 'active' || 
            $user->status == 'inactive');;
    }

    /**
     * Determine whether the user can create voucher.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user) {
        return $user->can('users.create') && $user->status == 'active';
    }

    /**
     * Determine whether the user can update the voucher.
     *
     * @param  \App\User  $user
     * @param  \App\Voucher  $voucher
     * @return mixed
     */
    public function update(User $user, Voucher $voucher) {
        return $user->can('users.update') && $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the voucher.
     *
     * @param  \App\User  $user
     * @param  \App\Voucher  $voucher
     * @return mixed
     */
    public function delete(User $user, Voucher $voucher) {
        return $user->can('users.delete') && $user->status == 'active';
    }
}
