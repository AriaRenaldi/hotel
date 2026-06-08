import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';

class VerifyOtpScreen extends StatefulWidget {
  final String email;
  final String type;

  const VerifyOtpScreen({super.key, required this.email, required this.type});

  @override
  State<VerifyOtpScreen> createState() => _VerifyOtpScreenState();
}

class _VerifyOtpScreenState extends State<VerifyOtpScreen> {
  final otp = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Verifikasi OTP')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Masukkan OTP untuk ${widget.email}'),
          const SizedBox(height: 12),
          TextField(controller: otp, decoration: const InputDecoration(labelText: 'Kode OTP')),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: app.isLoading
                ? null
                : () async {
                    final ok = await context.read<AppProvider>().verifyOtp(widget.email, otp.text.trim());
                    if (!context.mounted) return;
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text(ok ? 'OTP berhasil diverifikasi' : (app.errorMessage ?? 'Gagal verifikasi OTP'))),
                    );
                    if (ok) Navigator.pop(context);
                  },
            child: const Text('Verifikasi'),
          ),
          TextButton(
            onPressed: () async {
              final ok = await context.read<AppProvider>().resendOtp(
                    email: widget.email,
                    type: widget.type,
                    otpChannel: 'gmail',
                  );
              if (!context.mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text(ok ? 'OTP dikirim ulang' : (app.errorMessage ?? 'Gagal kirim ulang OTP'))),
              );
            },
            child: const Text('Kirim ulang OTP'),
          )
        ],
      ),
    );
  }
}
