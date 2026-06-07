<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminLoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $email,
    ) {}

    public function build()
    {
        return $this->subject('TechBits — Admin Panel Login Verification')
            ->view('emails.admin-login-otp');
    }
}
