<?php
namespace App\Repositories\Notification;

use App\Notification;
use App\User;

interface INotificationRepository
{
    /**
     * List all notifications
     * @param User $user 
     * @param array $data
     * @param boolean $paginate
     * @return [Notification] $notifications
     */
    public function list(User $user, $data, $paginate = false);

    /**
     * Create notification for users
     * @param [User] $users
     * @param integer $notification_log_id
     * @param array $notification_data
     * @return Notification $notification
     */
    public function create($users, $notification_log_id, $notification_data);

    /**
     * Log notification request
     * @param array $data
     * @return Notification $notification
     */
    public function log($data);

    /**
     * Mark a notification as read
     * @param Notification $notification
     * @return void
     */
    public function read(Notification $notification);

    /**
     * Mark all notifications of specified user as read
     * @param User $user
     * @return void
     */
    public function markAllAsRead(User $user);
}