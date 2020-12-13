<?php

namespace App\Policies;

use App\User;
use App\Feedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackPolicy {
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
     * Determine whether the user can view any feedbacks.
     *
     * @param  \App\Feedback $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('feedbacks.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can delete the feedback.
     *
     * @param  \App\User $user
     * @param  \App\Feedback $feedback
     * @return mixed
     */
    public function delete(User $user, Feedback $feedback) {
        return $user->can('feedbacks.delete') && 
            $user->status == 'active';
    }
}
