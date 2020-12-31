<?php

namespace App\Notifications\Announcement;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\Announcement;


class AnnouncementActioned extends Notification {
    private $announcement;
    
    public function __construct(Announcement $announcement) {
        $this->announcement = $announcement;
    }

    public function via($notifiable) {
        return [CustomFCMChannel::class];
    }

    public function toCustomFCM($notifiable) {
        $merchant = $notifiable;
        $title = '';
        $description = '';

        switch ($this->announcement->status) {
            case Announcement::STATUS_PUBLISHED:
                $title = 'Announcement Approved';
                $description = 'Your announcement "'.$this->announcement->title.'" has been approved and is published.';
                break;

            case Announcement::STATUS_REJECTED:
                $title = 'Announcement Rejected';
                $description = $this->announcement->remark;
                break;
        }

        $notification_data = [
            'title' => $title,
            'body' => $description,
            'redirect' => false
        ];

        return [
            'users' => $merchant->users,
            'payload' => $notification_data
        ];
    }
}
