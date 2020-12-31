<?php
namespace App\Repositories\Announcement;

use App\Announcement;
use App\User;

interface IAnnouncementRepository
{
    /**
     * List all announcements
     * @param User $user 
     * @param array $data
     * @param boolean $paginate
     * @return [Announcement] $announcements
     */
    public function list($data, $paginate = false);

    // /**
    //  * Create announcement for users
    //  * @param [User] $users
    //  * @param array $announcement_data
    //  * @return Announcement $announcement
    //  */
    // public function create($users, $announcement_data);

    // /**
    //  * Mark a announcement as read
    //  * @param Announcement $announcement
    //  * @return void
    //  */
    // public function read(Announcement $announcement);

    // /**
    //  * Mark all announcements of specified user as read
    //  * @param User $user
    //  * @return void
    //  */
    // public function markAllAsRead(User $user);
}