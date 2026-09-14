<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otpCode,
        public Carbon $expiresAt
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uthibitisho wa Barua Pepe - FUNDI App (' . $this->otpCode . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
