import 'dart:io';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../core/app_config.dart';
import '../../providers/app_provider.dart';
import '../auth/login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final fullName = TextEditingController();
  final phone = TextEditingController();
  final address = TextEditingController();
  final currentPassword = TextEditingController();
  final newPassword = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final user = app.user;
    if (user != null && fullName.text.isEmpty) {
      fullName.text = user.fullName;
      phone.text = user.phone ?? '';
      address.text = user.address ?? '';
    }

    return SafeArea(
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Profil', style: Theme.of(context).textTheme.headlineSmall),
          const SizedBox(height: 12),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 30,
                        backgroundColor: Colors.teal.shade50,
                        backgroundImage: _profileImage(user?.photoUrl),
                        child: user?.photoUrl == null ? const Icon(Icons.person) : null,
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(user?.fullName ?? '-', style: const TextStyle(fontWeight: FontWeight.bold)),
                            Text(user?.email ?? '-'),
                          ],
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    children: [
                      OutlinedButton(
                        onPressed: _pickAndUploadPhoto,
                        child: const Text('Upload Foto'),
                      ),
                      TextButton(
                        onPressed: () async {
                          final ok = await context.read<AppProvider>().deleteProfilePhoto();
                          if (!context.mounted) return;
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(content: Text(ok ? 'Foto profil dihapus' : (app.errorMessage ?? 'Gagal hapus foto'))),
                          );
                        },
                        child: const Text('Hapus Foto'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          TextField(controller: fullName, decoration: const InputDecoration(labelText: 'Nama lengkap')),
          const SizedBox(height: 8),
          TextField(controller: phone, decoration: const InputDecoration(labelText: 'No HP')),
          const SizedBox(height: 8),
          TextField(controller: address, decoration: const InputDecoration(labelText: 'Alamat')),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: user == null
                ? null
                : () async {
                    await context.read<AppProvider>().saveProfile(
                          fullName: fullName.text.trim(),
                          phone: phone.text.trim(),
                          address: address.text.trim(),
                        );
                    if (!context.mounted) return;
                    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profil diperbarui')));
                  },
            child: const Text('Simpan Profil'),
          ),
          const SizedBox(height: 16),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Ubah Password', style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  TextField(controller: currentPassword, obscureText: true, decoration: const InputDecoration(labelText: 'Password saat ini')),
                  const SizedBox(height: 8),
                  TextField(controller: newPassword, obscureText: true, decoration: const InputDecoration(labelText: 'Password baru')),
                  const SizedBox(height: 10),
                  FilledButton.tonal(
                    onPressed: () async {
                      final ok = await context.read<AppProvider>().changePassword(
                            currentPassword: currentPassword.text,
                            newPassword: newPassword.text,
                          );
                      if (!context.mounted) return;
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(ok ? 'Password berhasil diubah' : (app.errorMessage ?? 'Gagal ubah password'))),
                      );
                      if (ok) {
                        currentPassword.clear();
                        newPassword.clear();
                      }
                    },
                    child: const Text('Simpan Password Baru'),
                  )
                ],
              ),
            ),
          ),
          const SizedBox(height: 8),
          OutlinedButton(
            onPressed: () async {
              await context.read<AppProvider>().logout();
              if (!context.mounted) return;
              Navigator.of(context).pushAndRemoveUntil(
                MaterialPageRoute(builder: (_) => const LoginScreen()),
                (_) => false,
              );
            },
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }

  ImageProvider? _profileImage(String? url) {
    final resolved = AppConfig.absoluteUrl(url);
    if (resolved.isEmpty) return null;
    return CachedNetworkImageProvider(resolved);
  }

  Future<void> _pickAndUploadPhoto() async {
    final app = context.read<AppProvider>();
    final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (picked == null || !mounted) return;
    final ok = await app.uploadProfilePhoto(File(picked.path));
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(ok ? 'Foto profil diperbarui' : (app.errorMessage ?? 'Upload foto gagal'))),
    );
  }
}
