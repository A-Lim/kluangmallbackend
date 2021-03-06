<?php

namespace App\Notifications\Merchant;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\MerchantAccountTransaction;


class MerchantAccountToppedUp extends Notification {
    private $merchantAccountTransaction;
    
    public function __construct(MerchantAccountTransaction $merchantAccountTransaction) {
        $this->merchantAccountTransaction = $merchantAccountTransaction;
    }

    public function via($notifiable) {
        return [CustomFCMChannel::class];
    }

    public function toCustomFCM($notifiable) {
        $merchant = $notifiable;
        $users = $merchant->users;

        $notification_data = [
            'title' => 'Top Up Successful',
            'body' => $this->merchantAccountTransaction->credit.' has been creditted into your merchant account.',
            'redirect' => false,
        ];

        return [
            'users' => $users,
            'payload' => $notification_data
        ];
    }
}
