<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * One-time login code for the CPD Platform's "sign in with your registration
 * number" flow — sent to the practitioner's email on file here at MLCSCZ, not
 * to whatever address they typed on the CPD Platform (that's the whole point:
 * only someone with access to the real registered contact can complete it).
 */
class PractitionerLoginCodeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your CPD Platform sign-in code')
            ->greeting('Hello'.($notifiable->name ? " {$notifiable->name}" : '').',')
            ->line('Use this code to finish signing in to the CPD Platform with your MLCSCZ registration number:')
            ->line(new HtmlString('<h2 style="letter-spacing:4px;">'.$this->code.'</h2>'))
            ->line('This code expires in 10 minutes.')
            ->line("If you didn't request this, you can safely ignore this email — no one can sign in without it.");
    }
}
