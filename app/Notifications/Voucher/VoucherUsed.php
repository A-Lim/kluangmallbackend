<?php

namespace App\Notifications\Voucher;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\Voucher;
use App\MyVoucher;

class VoucherUsed extends Notification {
    private $voucher;
    
    public function __construct(Voucher $voucher) {
        $this->voucher = $voucher;
    }

    public function via($notifiable) {
        return [CustomFCMChannel::class];
    }

    public function toCustomFCM($notifiable) {
        // $merchant = $notifiable;
        // $title = '';
        // $description = '';

        // switch ($this->announcement->status) {
        //     case Announcement::STATUS_PUBLISHED:
        //         $title = 'Announcement Approved';
        //         $description = 'Your announcement "'.$this->announcement->title.'" has been approved and is published.';
        //         break;

        //     case Announcement::STATUS_REJECTED:
        //         $title = 'Announcement Rejected';
        //         $description = $this->announcement->remark;
        //         break;
        // }

        // $notification_data = [
        //     'title' => $title,
        //     'body' => $description,
        //     'redirect' => false
        // ];

        // return [
        //     'users' => $merchant->users,
        //     'payload' => $notification_data
        // ];
    }
}
