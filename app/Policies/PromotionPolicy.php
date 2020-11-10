<?php

namespace App\Policies;

use App\User;
use App\Promotion;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionPolicy {
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
     * Determine whether the user can view any promotions.
     *
     * @param  \App\Promotion $user
     * @return mixed
     */
    public function viewAny(Promotion $promotion) {
        return $user->can('promotions.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the promotion.
     *
     * @param  \App\User $user
     * @param  \App\Promotion $promotion
     * @return mixed
     */
    public function view(User $user, Promotion $promotion) {
        return $user->can('promotions.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create promotions.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(Promotion $promotion) {
        return $user->can('promotions.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the promotion.
     *
     * @param  \App\User $user
     * @param  \App\Promotion $promotion
     * @return mixed
     */
    public function update(User $user, Promotion $promotion) {
        return $user->can('promotions.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the promotion.
     *
     * @param  \App\User $user
     * @param  \App\Promotion $promotion
     * @return mixed
     */
    public function delete(User $user, Promotion $promotion) {
        return $user->can('promotions.delete') && 
            $user->status == 'active';
    }
}
