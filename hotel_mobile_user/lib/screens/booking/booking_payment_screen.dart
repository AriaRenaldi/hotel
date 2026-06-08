import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';

class BookingPaymentScreen extends StatefulWidget {
  final String bookingId;

  const BookingPaymentScreen({super.key, required this.bookingId});

  @override
  State<BookingPaymentScreen> createState() => _BookingPaymentScreenState();
}

class _BookingPaymentScreenState extends State<BookingPaymentScreen> {
  File? proof;
  final paidAmount = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await context.read<AppProvider>().openBookingDetail(widget.bookingId);
      if (!mounted) return;
      final detail = context.read<AppProvider>().selectedBooking;
      if (detail != null && paidAmount.text.isEmpty) {
        paidAmount.text = '${detail['total_amount'] ?? 0}';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final detail = app.selectedBooking;

    return Scaffold(
      appBar: AppBar(title: const Text('Pembayaran Booking')),
      body: detail == null
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Card(
                  child: ListTile(
                    title: Text(detail['booking_number']?.toString() ?? '-'),
                    subtitle: Text('Status bayar: ${detail['payment_status'] ?? '-'}'),
                    trailing: Text(_idr((detail['total_amount'] ?? 0) as num)),
                  ),
                ),
                const SizedBox(height: 8),
                TextField(
                  controller: paidAmount,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Nominal bayar'),
                ),
                const SizedBox(height: 8),
                OutlinedButton(
                  onPressed: () async {
                    final pick = await ImagePicker().pickImage(source: ImageSource.gallery);
                    if (pick == null) return;
                    setState(() => proof = File(pick.path));
                  },
                  child: Text(proof == null ? 'Pilih bukti pembayaran' : 'File: ${proof!.path.split('\\').last}'),
                ),
                const SizedBox(height: 10),
                FilledButton(
                  onPressed: () async {
                    if (proof == null) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Pilih bukti pembayaran dulu')),
                      );
                      return;
                    }
                    final amount = num.tryParse(paidAmount.text.trim()) ?? 0;
                    final ok = await context.read<AppProvider>().uploadPaymentProof(
                          widget.bookingId,
                          proof!,
                          amount,
                        );
                    if (!context.mounted) return;
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text(ok ? 'Bukti pembayaran berhasil diupload' : (app.errorMessage ?? 'Upload gagal'))),
                    );
                    if (ok) {
                      Navigator.pop(context);
                    }
                  },
                  child: const Text('Upload Bukti Bayar'),
                )
              ],
            ),
    );
  }

  String _idr(num value) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(value);
  }
}
