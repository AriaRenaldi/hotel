import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';
import 'verify_otp_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _username = TextEditingController();
  final _fullName = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _address = TextEditingController();
  final _password = TextEditingController();
  String otpChannel = 'gmail';

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Register')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: _username, decoration: const InputDecoration(labelText: 'Username')),
          const SizedBox(height: 12),
          TextField(controller: _fullName, decoration: const InputDecoration(labelText: 'Nama lengkap')),
          const SizedBox(height: 12),
          TextField(controller: _email, decoration: const InputDecoration(labelText: 'Email')),
          const SizedBox(height: 12),
          TextField(controller: _phone, decoration: const InputDecoration(labelText: 'No. HP')),
          const SizedBox(height: 12),
          TextField(controller: _address, decoration: const InputDecoration(labelText: 'Alamat')),
          const SizedBox(height: 12),
          TextField(controller: _password, obscureText: true, decoration: const InputDecoration(labelText: 'Password')),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            initialValue: otpChannel,
            decoration: const InputDecoration(labelText: 'Kirim OTP via'),
            items: const [
              DropdownMenuItem(value: 'gmail', child: Text('Gmail')),
              DropdownMenuItem(value: 'whatsapp', child: Text('WhatsApp')),
            ],
            onChanged: (v) => setState(() => otpChannel = v ?? 'gmail'),
          ),
          const SizedBox(height: 16),
          if (app.errorMessage != null)
            Text(app.errorMessage!, style: const TextStyle(color: Colors.red)),
          FilledButton(
            onPressed: app.isLoading
                ? null
                : () async {
                    final ok = await context.read<AppProvider>().register({
                      'username': _username.text.trim(),
                      'full_name': _fullName.text.trim(),
                      'email': _email.text.trim(),
                      'phone': _phone.text.trim(),
                      'address': _address.text.trim(),
                      'password': _password.text,
                      'password_confirmation': _password.text,
                      'role': 'guest',
                      'otp_channel': otpChannel,
                    });
                    if (!context.mounted) return;
                    if (ok) {
                      Navigator.of(context).push(
                        MaterialPageRoute(
                          builder: (_) => VerifyOtpScreen(
                            email: _email.text.trim(),
                            type: 'registration',
                          ),
                        ),
                      );
                    }
                  },
            child: const Text('Buat Akun'),
          ),
        ],
      ),
    );
  }
}
