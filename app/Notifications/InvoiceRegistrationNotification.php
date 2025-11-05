<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceRegistrationNotification extends Notification implements ShouldQueue
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
            ->greeting('Good day: '.$this->invoice->customer->name.' '.$this->invoice->customer->surname)
            ->line('Your registration invoice for '.$this->profession->name.' has been settled successfully and your registration now awaits approval. Once approved, you will receive a notification to proceed to make payment for your practising certificate.')
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
            "message"=>"Your registration invoice for ". $this->profession->name." has been settled successfully and your registration now awaits approval. Once approved, you will receive a notification to proceed to make payment for your practising certificate.",
        ];
    }
}
