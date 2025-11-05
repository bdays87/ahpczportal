<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $customer;
    protected $profession;
    protected $status;
    protected $comment;
    public function __construct($customer,$profession,$status,$comment)
    {
        $this->customer = $customer;
        $this->profession = $profession;
        $this->status = $status;
        $this->comment = $comment;
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
        if(strtoupper($this->status) == "APPROVED"){
            return (new MailMessage)
                ->greeting('Good day: '.$this->customer->name.' '.$this->customer->surname)
                ->line('Your application for '.$this->profession->name.' has been '.$this->status.' please login to download your certificates')
                ->action('Visit practitioner portal', route('login'))
                ->line('Thank you for using our application!');
            }else{
            return (new MailMessage)
                ->greeting('Good day: '.$this->customer->name.' '.$this->customer->surname)
                ->line('Your registration for '.$this->profession->name.' has been '.$this->status)
                ->line('Reason: '.$this->comment)
                ->line('Thank you for using our application!');
            }
        }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message"=>"Your application for ".$this->profession->name." has been ".$this->status." please login to download your certificates",
           
        ];
    }
}
