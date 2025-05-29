<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private string $fullname,
        private string $sujet
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Message : ' . $this->sujet,
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.test-email',
            with: [
                'fullname' => $this->fullname,
                'sujet' => $this->sujet
            ],
        );
    }
}
