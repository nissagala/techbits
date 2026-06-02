<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $email,
    ) {}

    public function build()
    {
        return $this->subject('TechBits — Verify your email')
            ->view('emails.registration-otp');
    }
}
