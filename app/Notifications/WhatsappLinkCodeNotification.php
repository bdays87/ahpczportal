<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Code a practitioner must send back on WhatsApp to link that phone number to their
 * MLCSCZ record. Sent to the email already on file, so only the real owner can complete it.
 */
class WhatsappLinkCodeNotification extends Notification
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
            ->subject('Your MLCSCZ WhatsApp verification code')
            ->greeting('Hello'.(($notifiable->name ?? null) ? " {$notifiable->name}" : '').',')
            ->line('Reply to the MLCSCZ WhatsApp chat with this code to link your phone number to your practitioner record:')
            ->line(new HtmlString('<h2 style="letter-spacing:4px;">'.$this->code.'</h2>'))
            ->line('This code expires in 10 minutes.')
            ->line("If you didn't ask for this, ignore this email. Nobody can use your record without the code.");
    }
}
