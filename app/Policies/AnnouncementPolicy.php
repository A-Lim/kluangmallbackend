<?php

namespace App\Policies;

use App\User;
use App\Announcement;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy {
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
     * Determine whether the user can view any announcements.
     *
     * @param  \App\Announcement $user
     * @return mixed
     */
    public function viewAny(User $user) {
        return $user->can('announcements.viewAny') && 
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can view the announcement.
     *
     * @param  \App\User $user
     * @param  \App\Announcement $announcement
     * @return mixed
     */
    public function view(User $user, Announcement $announcement) {
        return $user->can('announcements.view') &&
            ($user->status == 'active' || $user->status == 'inactive');
    }

    /**
     * Determine whether the user can create announcements.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user) {
        return $user->can('announcements.create') &&
            $user->status == 'active';
    }

    /**
     * Determine whether the user can take action to announcements
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function action(User $user) {
        return $user->can('announcements.action') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can update the announcement.
     *
     * @param  \App\User $user
     * @param  \App\Announcement $announcement
     * @return mixed
     */
    public function update(User $user, Announcement $announcement) {
        return $user->can('announcements.update') && 
            $user->status == 'active';
    }

    /**
     * Determine whether the user can delete the announcement.
     *
     * @param  \App\User $user
     * @param  \App\Announcement $announcement
     * @return mixed
     */
    public function delete(User $user, Announcement $announcement) {
        return $user->can('announcements.delete') && 
            $user->status == 'active';
    }
}
