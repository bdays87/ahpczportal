<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalPromotionalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public string $surname;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name = '', string $surname = '')
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to '.config('app.name').' Portal - Register Now!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-promotional',
            with: [
                'name' => $this->name,
                'surname' => $this->surname,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
