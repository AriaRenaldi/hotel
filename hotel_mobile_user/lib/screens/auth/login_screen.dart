import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';
import '../booking/public_check_booking_screen.dart';
import 'forgot_reset_screen.dart';
import 'register_screen.dart';
import '../home/main_shell_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _email = TextEditingController();
  final _password = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0D5C63), Color(0xFF44A1A0)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                const Spacer(),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Welcome Back', style: Theme.of(context).textTheme.headlineSmall),
                        const SizedBox(height: 6),
                        const Text('Login untuk booking hotel dari handphone.'),
                        const SizedBox(height: 16),
                        TextField(controller: _email, decoration: const InputDecoration(labelText: 'Email')),
                        const SizedBox(height: 12),
                        TextField(controller: _password, obscureText: true, decoration: const InputDecoration(labelText: 'Password')),
                        const SizedBox(height: 16),
                        if (app.errorMessage != null)
                          Text(app.errorMessage!, style: const TextStyle(color: Colors.red)),
                        const SizedBox(height: 8),
                        SizedBox(
                          width: double.infinity,
                          child: FilledButton(
                            onPressed: app.isLoading
                                ? null
                                : () async {
                                    final ok = await context.read<AppProvider>().login(_email.text.trim(), _password.text);
                                    if (!context.mounted) return;
                                    if (ok) {
                                      Navigator.of(context).pushReplacement(
                                        MaterialPageRoute(builder: (_) => const MainShellScreen()),
                                      );
                                    }
                                  },
                            child: app.isLoading
                                ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2))
                                : const Text('Login'),
                          ),
                        ),
                        TextButton(
                          onPressed: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RegisterScreen())),
                          child: const Text('Belum punya akun? Register'),
                        ),
                        TextButton(
                          onPressed: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ForgotResetScreen())),
                          child: const Text('Lupa password?'),
                        ),
                        TextButton(
                          onPressed: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const PublicCheckBookingScreen())),
                          child: const Text('Cek booking tanpa login'),
                        ),
                      ],
                    ),
                  ),
                ),
                const Spacer(),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
