<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EmailVerification extends Notification
{
    use Queueable;

    private $otp;
    private $email;

    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $otpString = new HtmlString('OTP IS : <strong>' . $this->otp . '</strong>');
        $urlVerify = config('consts.backend_url') . "/api/mobile/verify_otp?email=" . $this->email . "&otp=" . $this->otp;
        return (new MailMessage)
            ->greeting('Notification for Verify Account !')
            ->line('Please insert the OTP in this application, for verify your account.')
            ->line($otpString);
    }


    public function toArray($notifiable)
    {
        return [];
    }
}
