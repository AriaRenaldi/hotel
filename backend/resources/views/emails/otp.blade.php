<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP - Hotel Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: white;
            font-size: 28px;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            margin: 10px 0 0;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .otp-code {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1e3c72;
            border-radius: 12px;
            margin: 25px 0;
            font-family: monospace;
        }
        .message {
            margin: 20px 0;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            font-size: 13px;
            color: #856404;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Hotel Management System</h1>
            <p>Sistem Manajemen Hotel Terintegrasi</p>
        </div>
        
        <div class="content">
            @if($type == 'registration')
                <div class="greeting">
                    Halo <strong>{{ $fullName }}</strong>!
                </div>
                <div class="message">
                    Terima kasih telah mendaftar di Hotel Management System. Untuk menyelesaikan proses registrasi, 
                    silakan masukkan kode OTP berikut:
                </div>
            @else
                <div class="greeting">
                    Halo!
                </div>
                <div class="message">
                    Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP berikut untuk mereset password:
                </div>
            @endif
            
            <div class="otp-code">
                {{ $otpCode }}
            </div>
            
            <div class="message">
                Kode OTP ini berlaku selama <strong>5 menit</strong>.
            </div>
            
            <div class="warning">
                ⚠️ <strong>Peringatan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapapun, termasuk pihak yang mengaku dari Hotel Management System.
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Hotel Management System. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>