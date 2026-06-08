import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';
import 'booking_print_screen.dart';

class PublicCheckBookingScreen extends StatefulWidget {
  const PublicCheckBookingScreen({super.key});

  @override
  State<PublicCheckBookingScreen> createState() => _PublicCheckBookingScreenState();
}

class _PublicCheckBookingScreenState extends State<PublicCheckBookingScreen> {
  final bookingNumber = TextEditingController();
  final email = TextEditingController();
  Map<String, dynamic>? result;

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Cek Booking Publik')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: bookingNumber, decoration: const InputDecoration(labelText: 'Nomor Booking')),
          const SizedBox(height: 8),
          TextField(controller: email, decoration: const InputDecoration(labelText: 'Email')),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: () async {
              final data = await context.read<AppProvider>().checkBookingPublic(
                    bookingNumber: bookingNumber.text.trim(),
                    email: email.text.trim(),
                  );
              if (!context.mounted) return;
              setState(() => result = data);
              if (data == null) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text(app.errorMessage ?? 'Data tidak ditemukan')),
                );
              }
            },
            child: const Text('Cek Booking'),
          ),
          const SizedBox(height: 12),
          if (result != null)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Booking: ${result!['booking_number'] ?? '-'}'),
                    Text('Status: ${result!['booking_status'] ?? '-'}'),
                    Text('Check-in: ${result!['check_in_date'] ?? '-'}'),
                    Text('Check-out: ${result!['check_out_date'] ?? '-'}'),
                    Text('Total: ${result!['total_amount'] ?? '-'}'),
                    const SizedBox(height: 8),
                    FilledButton.tonal(
                      onPressed: () async {
                        final bookingId = result!['id']?.toString() ?? '';
                        if (bookingId.isEmpty) return;
                        await context.read<AppProvider>().openPublicPrintableBooking(bookingId);
                        if (!context.mounted) return;
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => BookingPrintScreen(
                              bookingId: bookingId,
                              usePublicEndpoint: true,
                            ),
                          ),
                        );
                      },
                      child: const Text('Lihat Struk Publik'),
                    )
                  ],
                ),
              ),
            )
        ],
      ),
    );
  }
}
