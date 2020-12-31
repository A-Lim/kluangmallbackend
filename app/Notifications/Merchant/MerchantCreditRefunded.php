<?php

namespace App\Notifications\Merchant;

use Illuminate\Notifications\Notification;

use App\Channels\CustomFCMChannel;
use App\MerchantAccountTransaction;


class MerchantCreditRefunded extends Notification {
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
            'title' => 'Credit Refunded',
            'body' => $this->merchantAccountTransaction->credit.' credits has been deducted for the refund.',
            'redirect' => false,
        ];

        return [
            'users' => $users,
            'payload' => $notification_data
        ];
    }
}
