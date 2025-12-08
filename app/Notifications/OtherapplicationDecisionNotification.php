<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtherapplicationDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $otherapplication;
    public $customer;
    protected $status;
    public function __construct($customer,$otherapplication,$status)
    {
        $this->otherapplication = $otherapplication;
        $this->customer = $customer;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Good day: '.$this->customer->name.' '.$this->customer->surname)
            ->line('Your other service application for '.$this->otherapplication->otherservice->name.' has been '.$this->status.' please login to proceed to view your application')
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
            //
        ];
    }
}
