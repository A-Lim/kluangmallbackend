<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\ResetPassword;

class MerchantWelcome extends ResetPassword {

    public function __construct($token) {
        parent::__construct($token);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }
        
        // frontend url for reset password
        $param = ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()];
        $query = http_build_query($param);
        $url = url(config('app.frontend_url').'/reset-password?'.$query);

        return (new MailMessage)
            ->subject(Lang::get('Merchant Account'))
            ->line(Lang::get('Your merchant account has been successfully created. 
                Use the link below to set the password for your new merchant account.'))
            ->action(Lang::get('Set Password'), $url);
            // ->line(Lang::get('This password reset link will expire in :count minutes.', 
            //     ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]));
    }
}
