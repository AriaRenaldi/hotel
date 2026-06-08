<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private const OTP_CHANNEL_GMAIL = 'gmail';
    private const OTP_CHANNEL_WHATSAPP = 'whatsapp';

    /**
     * Public: daftar metode verifikasi OTP.
     */
    public function otpChannels()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'channels' => [
                    ['value' => self::OTP_CHANNEL_GMAIL, 'label' => 'Gmail'],
                    ['value' => self::OTP_CHANNEL_WHATSAPP, 'label' => 'WhatsApp'],
                ],
            ],
        ]);
    }

    /**
     * Register user dan kirim OTP sesuai channel pilihan.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $otpChannel = $this->resolveOtpChannel($request->input('otp_channel'));

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
                'is_verified' => false,
            ]);

            $this->assertOtpChannelReady($user, $otpChannel);
            $otpCode = $this->generateAndStoreOtp($user);
            $this->sendOtpByChannel($user, $otpCode, 'registration', $otpChannel);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Kode OTP telah dikirim via ' . $this->channelLabel($otpChannel) . '.',
                'data' => [
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'otp_destination' => $otpChannel === self::OTP_CHANNEL_WHATSAPP ? $user->phone : $user->email,
                    'otp_channel' => $otpChannel,
                    'expires_in' => 5,
                    'available_verification_methods' => [self::OTP_CHANNEL_GMAIL, self::OTP_CHANNEL_WHATSAPP],
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('register exception', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verifikasi OTP (umum) setelah registrasi.
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                ], 404);
            }

            if ($user->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terverifikasi.',
                ], 400);
            }

            if (!$user->otp || !Hash::check($request->otp_code, $user->otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid.',
                ], 400);
            }

            if (now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP sudah kadaluarsa.',
                ], 400);
            }

            $user->update([
                'is_verified' => true,
                'email_verified_at' => now(),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi berhasil! Silakan login.',
            ]);
        } catch (\Exception $e) {
            \Log::error('verifyOtp exception', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verifikasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint lama OTP login dinonaktifkan.
     */
    public function verifyLoginOtp(VerifyOtpRequest $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Verifikasi OTP saat login sudah dinonaktifkan. Silakan login langsung dengan email dan password.',
        ], 410);
    }

    /**
     * Resend OTP untuk registration/reset_password sesuai channel pilihan.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:registration,reset_password',
            'otp_channel' => 'required|in:gmail,whatsapp',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak terdaftar.',
                ], 404);
            }

            if ($request->type === 'registration' && $user->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terverifikasi.',
                ], 400);
            }

            $otpChannel = $this->resolveOtpChannel($request->input('otp_channel'));
            $this->assertOtpChannelReady($user, $otpChannel);

            $otpCode = $this->generateAndStoreOtp($user);
            $this->sendOtpByChannel($user, $otpCode, $request->type, $otpChannel);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP baru telah dikirim via ' . $this->channelLabel($otpChannel) . '.',
                'data' => [
                    'expires_in' => 5,
                    'otp_channel' => $otpChannel,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user tanpa OTP tambahan.
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            if (!$user->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email belum terverifikasi. Silakan verifikasi OTP registrasi terlebih dahulu.',
                    'data' => [
                        'email' => $user->email,
                        'is_verified' => false,
                        'available_verification_methods' => [self::OTP_CHANNEL_GMAIL, self::OTP_CHANNEL_WHATSAPP],
                    ]
                ], 403);
            }

            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'role' => $user->role,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'photo' => $user->photo,
                        'photo_url' => $user->photo_url,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forgot password - kirim OTP reset password sesuai channel pilihan.
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $otpChannel = $this->resolveOtpChannel($request->input('otp_channel'));
            $email = trim((string) $request->input('email'));
            $phone = trim((string) $request->input('phone'));

            $user = $otpChannel === self::OTP_CHANNEL_WHATSAPP
                ? User::where('phone', $phone)->first()
                : User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => $otpChannel === self::OTP_CHANNEL_WHATSAPP
                        ? 'Nomor telepon/WhatsApp tidak ditemukan.'
                        : 'Email tidak ditemukan.',
                ], 404);
            }

            // Jika dua field diisi bersamaan, pastikan mengacu ke user yang sama.
            if ($email !== '' && strcasecmp($user->email, $email) !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email dan nomor telepon tidak sesuai dengan akun yang sama.',
                ], 422);
            }

            if ($phone !== '' && (string) $user->phone !== $phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email dan nomor telepon tidak sesuai dengan akun yang sama.',
                ], 422);
            }

            $this->assertOtpChannelReady($user, $otpChannel);
            $otpCode = $this->generateAndStoreOtp($user);
            $this->sendOtpByChannel($user, $otpCode, 'reset_password', $otpChannel);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP reset password telah dikirim via ' . $this->channelLabel($otpChannel) . '.',
                'data' => [
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'otp_destination' => $otpChannel === self::OTP_CHANNEL_WHATSAPP ? $user->phone : $user->email,
                    'otp_channel' => $otpChannel,
                    'expires_in' => 5,
                    'available_verification_methods' => [self::OTP_CHANNEL_GMAIL, self::OTP_CHANNEL_WHATSAPP],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password dengan OTP.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                ], 404);
            }

            if (!$user->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan request reset password terlebih dahulu.',
                ], 400);
            }

            if (!Hash::check($request->otp_code, $user->otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid.',
                ], 400);
            }

            if (now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP sudah kadaluarsa.',
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset! Silakan login dengan password baru.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reset password gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
            ]
        ]);
    }

    private function resolveOtpChannel(?string $channel): string
    {
        $normalized = strtolower((string) $channel);
        if ($normalized === self::OTP_CHANNEL_WHATSAPP) {
            return self::OTP_CHANNEL_WHATSAPP;
        }

        return self::OTP_CHANNEL_GMAIL;
    }

    private function channelLabel(string $channel): string
    {
        return $channel === self::OTP_CHANNEL_WHATSAPP ? 'WhatsApp' : 'Gmail';
    }

    private function assertOtpChannelReady(User $user, string $channel): void
    {
        if ($channel !== self::OTP_CHANNEL_WHATSAPP) {
            return;
        }

        if (!$user->phone) {
            throw new \RuntimeException('Nomor WhatsApp wajib diisi untuk verifikasi via WhatsApp.');
        }
    }

    private function generateAndStoreOtp(User $user): string
    {
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp' => Hash::make($otpCode),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        return $otpCode;
    }

    private function sendOtpByChannel(User $user, string $otpCode, string $type, string $channel): void
    {
        if ($channel === self::OTP_CHANNEL_WHATSAPP) {
            $sent = $this->sendOtpToWhatsApp($user->phone, $otpCode, $type);
            if (!$sent) {
                throw new \RuntimeException('Pengiriman OTP WhatsApp gagal. Periksa konfigurasi Fonnte.');
            }
            return;
        }

        Mail::to($user->email)->send(new OtpMail($otpCode, $type, $user->full_name));
    }

    /**
     * Send OTP via WhatsApp if FONNTE config exists.
     */
    private function sendOtpToWhatsApp(?string $phone, string $otpCode, string $type): bool
    {
        if (!$phone) {
            return false;
        }

        $destination = $this->normalizeWhatsAppNumber($phone);
        if (!$destination) {
            return false;
        }

        $fonnteUrl = env('FONNTE_URL') ?: 'https://api.fonnte.com/send';
        $fonnteToken = env('FONNTE_TOKEN');
        $fonnteApiKey = env('FONNTE_API_KEY');

        if (!$fonnteUrl || !$fonnteToken) {
            return false;
        }

        $message = match ($type) {
            'registration' => "Kode OTP registrasi Anda: {$otpCode}",
            'reset_password' => "Kode OTP reset password Anda: {$otpCode}",
            default => "Kode OTP Anda: {$otpCode}",
        };

        try {
            $headers = [
                'FONNTE-TOKEN' => $fonnteToken,
                'Authorization' => $fonnteToken,
            ];

            if (!empty($fonnteApiKey)) {
                $headers['FONNTE-API-KEY'] = $fonnteApiKey;
            }

            $response = Http::asForm()
                ->withHeaders($headers)
                ->post($fonnteUrl, [
                    'target' => $destination,
                    'destination' => $destination,
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                return false;
            }

            $payload = $response->json();
            if (is_array($payload) && array_key_exists('status', $payload)) {
                return (bool) $payload['status'];
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function normalizeWhatsAppNumber(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '62')) {
            $digits = '62' . ltrim($digits, '0');
        }

        return strlen($digits) >= 10 ? $digits : null;
    }
}
