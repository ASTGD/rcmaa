<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlumniAccessLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registration $registration,
        public string $url,
        public int $minutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your RCMAA registration — secure access link');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.alumni-access-link');
    }
}
