<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp_channel' => 'required|in:gmail,whatsapp',
            'email' => 'required_if:otp_channel,gmail|nullable|email|exists:users,email',
            'phone' => 'required_if:otp_channel,whatsapp|nullable|string|exists:users,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Email tidak terdaftar dalam sistem',
            'email.required_if' => 'Email wajib diisi untuk verifikasi OTP via Gmail',
            'phone.required_if' => 'Nomor telepon/WhatsApp wajib diisi untuk verifikasi OTP via WhatsApp',
            'phone.exists' => 'Nomor telepon/WhatsApp tidak terdaftar dalam sistem',
            'otp_channel.required' => 'Metode verifikasi OTP wajib dipilih',
            'otp_channel.in' => 'Metode verifikasi OTP tidak valid',
        ];
    }
}
