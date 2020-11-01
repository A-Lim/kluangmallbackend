<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\User;

class ForgetPasswordOTP extends Notification {
    use Queueable;

    private User $user;
    private $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $otp) {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Forget Passport OTP')
            ->greeting('Hello '.$this->user->name.',')
            ->line('The OTP for your forget password is: '.$this->otp)
            ->line('OTP will only be valid for 5 minutes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            //
        ];
    }
}
