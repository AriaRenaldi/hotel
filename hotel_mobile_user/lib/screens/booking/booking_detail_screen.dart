import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';
import 'booking_payment_screen.dart';
import 'booking_print_screen.dart';
import 'penalty_payment_screen.dart';

class BookingDetailScreen extends StatefulWidget {
  final String bookingId;

  const BookingDetailScreen({super.key, required this.bookingId});

  @override
  State<BookingDetailScreen> createState() => _BookingDetailScreenState();
}

class _BookingDetailScreenState extends State<BookingDetailScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AppProvider>().openBookingDetail(widget.bookingId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final detail = app.selectedBooking;

    return Scaffold(
      appBar: AppBar(title: const Text('Detail Booking')),
      body: detail == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () => context.read<AppProvider>().openBookingDetail(widget.bookingId),
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  _summaryCard(detail),
                  const SizedBox(height: 8),
                  _stayInfoCard(detail),
                  const SizedBox(height: 8),
                  _paymentInfoCard(detail),
                  const SizedBox(height: 8),
                  if (app.errorMessage != null)
                    Text(app.errorMessage!, style: const TextStyle(color: Colors.red)),
                ],
              ),
            ),
    );
  }

  Widget _summaryCard(Map<String, dynamic> detail) {
    final status = detail['booking_status']?.toString() ?? '-';
    final paymentStatus = detail['payment_status']?.toString() ?? '-';

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(detail['booking_number']?.toString() ?? '-', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                _chip('Status: $status'),
                _chip('Payment: $paymentStatus'),
                _chip('Metode: ${detail['payment_method'] ?? '-'}'),
              ],
            ),
            const SizedBox(height: 10),
            Text('Total: ${_idr((detail['total_amount'] ?? 0) as num)}'),
          ],
        ),
      ),
    );
  }

  Widget _stayInfoCard(Map<String, dynamic> detail) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Info Menginap', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            Text('Check-in: ${detail['check_in_date'] ?? '-'}'),
            Text('Check-out: ${detail['check_out_date'] ?? '-'}'),
            Text('Jam check-in pilihan: ${detail['requested_checkin_time'] ?? '-'}'),
            Text('Kamar: ${detail['total_rooms'] ?? '-'}'),
            if (detail['actual_check_in_at'] != null)
              Text('Actual check-in: ${detail['actual_check_in_at']}'),
            if (detail['actual_check_out_at'] != null)
              Text('Actual check-out: ${detail['actual_check_out_at']}'),
            if ((detail['late_checkout_penalty'] ?? 0) != 0)
              Text('Denda telat: ${_idr((detail['late_checkout_penalty'] ?? 0) as num)}'),
          ],
        ),
      ),
    );
  }

  Widget _paymentInfoCard(Map<String, dynamic> detail) {
    final isConfirmed = detail['booking_status'] == 'confirmed';
    final isCheckedIn = detail['booking_status'] == 'checked_in';
    final isQris = detail['payment_method'] == 'qris';
    final isPaymentPending = ['pending', 'rejected'].contains(detail['payment_status']);
    final isPaymentVerified = detail['payment_status'] == 'verified';

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Aksi Booking', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            if (isConfirmed && isQris && isPaymentPending)
              FilledButton(
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => BookingPaymentScreen(bookingId: widget.bookingId),
                  ),
                ),
                child: const Text('Upload Bukti Bayar QRIS'),
              ),
            if (isConfirmed && isPaymentVerified)
              OutlinedButton(
                onPressed: _uploadCheckInReceipt,
                child: const Text('Upload Struk Check-in'),
              ),
            if (isConfirmed)
              TextButton(
                onPressed: () async {
                  await context.read<AppProvider>().cancelBooking(widget.bookingId);
                  if (!mounted) return;
                  Navigator.pop(context);
                },
                child: const Text('Batalkan Booking'),
              ),
            if (isCheckedIn)
              FilledButton(
                onPressed: _processCheckout,
                child: const Text('Check-out'),
              ),
            OutlinedButton(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => BookingPrintScreen(bookingId: widget.bookingId),
                ),
              ),
              child: const Text('Lihat Printable + Barcode'),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _uploadCheckInReceipt() async {
    final app = context.read<AppProvider>();
    final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (picked == null || !mounted) return;

    final ok = await app.uploadCheckInReceipt(widget.bookingId, File(picked.path));
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(ok ? 'Struk check-in berhasil diupload' : (app.errorMessage ?? 'Upload gagal'))),
    );
  }

  Future<void> _processCheckout() async {
    final app = context.read<AppProvider>();
    final result = await app.checkOut(widget.bookingId);
    if (!mounted) return;

    if (result == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(app.errorMessage ?? 'Check-out gagal')),
      );
      return;
    }

    final requiresPenalty = result['requires_penalty_payment'] == true;
    if (requiresPenalty) {
      Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => PenaltyPaymentScreen(bookingId: widget.bookingId)),
      );
      return;
    }

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Check-out berhasil diproses')),
    );
  }

  Widget _chip(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.teal.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(99),
      ),
      child: Text(text, style: const TextStyle(fontSize: 12)),
    );
  }

  String _idr(num value) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(value);
  }
}
