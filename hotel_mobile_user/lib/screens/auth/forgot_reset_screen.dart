import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';

class ForgotResetScreen extends StatefulWidget {
  const ForgotResetScreen({super.key});

  @override
  State<ForgotResetScreen> createState() => _ForgotResetScreenState();
}

class _ForgotResetScreenState extends State<ForgotResetScreen> {
  final identifier = TextEditingController();
  final emailForReset = TextEditingController();
  final otp = TextEditingController();
  final newPassword = TextEditingController();
  String otpChannel = 'gmail';

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final usingGmail = otpChannel == 'gmail';

    return Scaffold(
      appBar: AppBar(title: const Text('Forgot / Reset Password')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          DropdownButtonFormField<String>(
            initialValue: otpChannel,
            decoration: const InputDecoration(labelText: 'OTP channel'),
            items: const [
              DropdownMenuItem(value: 'gmail', child: Text('Gmail')),
              DropdownMenuItem(value: 'whatsapp', child: Text('WhatsApp')),
            ],
            onChanged: (v) => setState(() => otpChannel = v ?? 'gmail'),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: identifier,
            decoration: InputDecoration(labelText: usingGmail ? 'Email' : 'No. WhatsApp'),
          ),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: () async {
              if (usingGmail) {
                final ok = await context.read<AppProvider>().forgotPassword(identifier.text.trim());
                if (!context.mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text(ok ? 'OTP reset dikirim' : (app.errorMessage ?? 'Gagal kirim OTP'))),
                );
                if (ok) emailForReset.text = identifier.text.trim();
              } else {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Reset via WhatsApp belum diaktifkan di layar ini. Pakai Gmail dulu.')),
                );
              }
            },
            child: const Text('Kirim OTP Reset'),
          ),
          const Divider(height: 28),
          TextField(controller: emailForReset, decoration: const InputDecoration(labelText: 'Email akun (untuk reset)')),
          const SizedBox(height: 8),
          TextField(controller: otp, decoration: const InputDecoration(labelText: 'OTP')),
          const SizedBox(height: 8),
          TextField(controller: newPassword, obscureText: true, decoration: const InputDecoration(labelText: 'Password baru')),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: () async {
              final ok = await context.read<AppProvider>().resetPassword(
                    email: emailForReset.text.trim(),
                    otpCode: otp.text.trim(),
                    newPassword: newPassword.text,
                  );
              if (!context.mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text(ok ? 'Password berhasil direset' : (app.errorMessage ?? 'Reset gagal'))),
              );
              if (ok) Navigator.pop(context);
            },
            child: const Text('Reset Password'),
          )
        ],
      ),
    );
  }
}
