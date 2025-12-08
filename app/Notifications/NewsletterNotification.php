<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newsletter;

    protected $customer;

    public function __construct(Newsletter $newsletter, Customer $customer)
    {
        $this->newsletter = $newsletter;
        $this->customer = $customer;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->newsletter->title)
            ->greeting('Dear '.$this->customer->name.' '.$this->customer->surname)
            ->line('We have a new newsletter for you!')
            ->action('Read Newsletter', $this->newsletter->link)
            ->line('Thank you for being part of our community!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New newsletter: '.$this->newsletter->title,
            'link' => $this->newsletter->link,
            'newsletter_id' => $this->newsletter->id,
        ];
    }
}
