<?php

namespace App\Notifications\Voucher;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\MyVoucher;

class VoucherUsed extends Notification {
    private $myVoucher;
    
    public function __construct(MyVoucher $myVoucher) {
        $this->myVoucher = $myVoucher;
    }

    public function via($notifiable) {
        return [CustomFCMChannel::class];
    }

    public function toCustomFCM($notifiable) {
        $user = $notifiable;

        $notification_data = [
            'title' => 'Voucher successfully used.',
            'body' => 'You have used your ['.$this->myVoucher->merchant->name.'] '.$this->myVoucher->voucher->name.' voucher.',
            'redirect' => true,
            'type' => 'myvouchers',
            'type_id' => $this->myVoucher->id,
        ];

        return [
            'users' => collect([$user]),
            'payload' => $notification_data
        ];
    }
}
