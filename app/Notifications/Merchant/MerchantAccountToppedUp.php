<?php

namespace App\Notifications\Merchant;

use Illuminate\Notifications\Notification;

use App\MerchantAccountTransaction;

class MerchantAccountToppedUp extends Notification {
    private $merchantAccountTransaction;

    public function __construct(MerchantAccountTransaction $merchantAccountTransaction) {
        $this->merchantAccountTransaction = $merchantAccountTransaction;
    }

    public function via($notifiable) {
        // return ['mail'];
    }

    public function toMail($notifiable) {

    }
}
