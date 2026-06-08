<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $type;
    public $fullName;

    public function __construct($otpCode, $type, $fullName = null)
    {
        $this->otpCode = $otpCode;
        $this->type = $type;
        $this->fullName = $fullName;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'registration' => 'Verifikasi Email - Hotel Management System',
            'reset_password' => 'Reset Password - Hotel Management System',
            'login' => 'Verifikasi Login - Hotel Management System',
            default => 'OTP Code - Hotel Management System'
        };
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}