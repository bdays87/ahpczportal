<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WallettopupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $name;
    protected $amount;
    protected $currency;

    public function __construct($name,$amount,$currency)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Hello ' . $this->name . ',')
            ->line('Your wallet has been topped up successfully.')
            ->line('Amount: ' . $this->amount . ' ' . $this->currency)
            ->action('Login', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message"=>"Your wallet has been topped up successfully.",
        ];
    }
}
