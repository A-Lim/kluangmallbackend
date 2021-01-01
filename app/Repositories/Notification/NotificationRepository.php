<?php
namespace App\Repositories\Notification;

use DB;
use Carbon\Carbon;
use App\User;
use App\Merchant;
use App\Notification;
use App\NotificationLog;

class NotificationRepository implements INotificationRepository {

    /**
     * {@inheritdoc}
     */
    public function list(User $user, $data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Notification::buildQuery($data);
        else 
            $query = Notification::query()->orderBy('id', 'desc');

        $query->where('user_id', $user->id)
            ->orderBy('id', 'desc');
        
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }
    
    /**
     * {@inheritdoc}
     */
    public function create($users, $notification_log_id, $notification_data) {
        $data = [];
        // remove redundant data from payload
        $payload = $notification_data;
        unset($payload['title']);
        unset($payload['body']);

        foreach ($users as $user) {
            array_push($data, [
                'user_id' => $user->id,
                'notification_log_id' => $notification_log_id,
                'title' => $notification_data['title'],
                'description' => $notification_data['body'],
                'payload' => json_encode($payload),
                'created_at' => Carbon::now()
            ]);
        }

        Notification::insert($data);
    }

    /**
     * {@inheritdoc}
     */
    public function log($data) {
        return NotificationLog::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read(Notification $notification) {
        $notification->read = true;
        $notification->save();
    }

    /**
     * {@inheritdoc}
     */
    public function markAllAsRead(User $user) {
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Notification $notification) {
        $notification->delete();
    }
}