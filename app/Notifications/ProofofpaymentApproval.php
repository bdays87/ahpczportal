<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProofofpaymentApproval extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice; 
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
            ->line('A customer named '.$this->invoice->customer->name.' '.$this->invoice->customer->surname.' has made upload proof of payment for invoice number: '.$this->invoice->invoice_number)
            ->action('View Invoice', route('login'))
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
            "message"=>"A customer named ".$this->invoice->customer->name." ".$this->invoice->customer->surname." has made upload proof of payment for invoice number: ".$this->invoice->invoice_number,
        ];
    }
}
