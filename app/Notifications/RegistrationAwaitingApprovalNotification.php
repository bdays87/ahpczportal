<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationAwaitingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $invoice;
    protected $profession;
    public function __construct($invoice,$profession)
    {
        $this->invoice = $invoice;
        $this->profession = $profession;
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
            ->greeting('Good day: '.$notifiable->name.' '.$notifiable->surname)
            ->line('A new registration for '.$this->invoice->customer->name.' '.$this->invoice->customer->surname.' for the profession of '.$this->profession->name.' has been settled successfully and is awaiting approval')
            ->action('Visit practitioner portal', route('login'))
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
            "message"=>"A new registration for ". $this->invoice->customer->name." ". $this->invoice->customer->surname." for the profession of ". $this->profession->name." has been settled successfully and is awaiting approval",
        ];
    }
}
