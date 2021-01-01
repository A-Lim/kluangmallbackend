<?php

namespace App\Notifications\Announcement;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\Announcement;


class AnnouncementPublished extends Notification {
    private $announcement;
    
    public function __construct(Announcement $announcement) {
        $this->announcement = $announcement;
    }

    public function via($notifiable) {
        return [CustomFCMChannel::class];
    }

    public function toCustomFCM($notifiable) {
        $notification_data = [
            'title' => $this->announcement->title,
            'body' => $this->announcement->description,
            'redirect' => $this->announcement->has_content,
        ];

        if ($this->announcement->has_content) {
            $notification_data['type'] = 'announcement';
            $notification_data['type_id'] = $this->announcement->id;
        }

        return [
            'topic' => $this->announcement->audience,
            'payload' => $notification_data
        ];
    }
}
