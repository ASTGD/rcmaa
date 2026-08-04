<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Registration received — {$this->registration->reference} | RCMAA Grand Reunion 2026",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registration-received');
    }
}
