<?php

namespace App\Policies;

use App\User;
use App\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy {
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
     * Determine whether the user can view any events.
     *
     * @param  \App\Event $user
     * @return mixed
     */
    public function viewAny(Event $event) {
        return $user->can('events.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the event.
     *
     * @param  \App\User $user
     * @param  \App\Event $event
     * @return mixed
     */
    public function view(User $user, Event $event) {
        return $user->can('events.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create events.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(Event $event) {
        return $user->can('events.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the event.
     *
     * @param  \App\User $user
     * @param  \App\Event $event
     * @return mixed
     */
    public function update(User $user, Event $event) {
        return $user->can('events.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the event.
     *
     * @param  \App\User $user
     * @param  \App\Event $event
     * @return mixed
     */
    public function delete(User $user, Event $event) {
        return $user->can('events.delete') && 
            $user->status == 'active';
    }
}
