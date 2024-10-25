<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public string $name;
    public string $password;
    public string $url;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $name, $password)
    {
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;
        $this->url = env('APP_URL');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'no-reply@opengrc.com',
            to: $this->email,
            subject: 'Welcome to OpenGRC',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user_created', // Your email Blade template
            with: [
                'url' => $this->url,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
