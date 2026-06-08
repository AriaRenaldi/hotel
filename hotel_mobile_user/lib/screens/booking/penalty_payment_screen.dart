import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';

class PenaltyPaymentScreen extends StatefulWidget {
  final String bookingId;

  const PenaltyPaymentScreen({super.key, required this.bookingId});

  @override
  State<PenaltyPaymentScreen> createState() => _PenaltyPaymentScreenState();
}

class _PenaltyPaymentScreenState extends State<PenaltyPaymentScreen> {
  String method = 'qris';
  final paidAmount = TextEditingController();
  File? proof;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await context.read<AppProvider>().openLateCheckoutPenaltyDetail(widget.bookingId);
      if (!mounted) return;
      final data = context.read<AppProvider>().selectedPenaltyDetail;
      if (data != null) {
        paidAmount.text = '${data['late_checkout_penalty'] ?? 0}';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final data = app.selectedPenaltyDetail;

    return Scaffold(
      appBar: AppBar(title: const Text('Bayar Denda Checkout')),
      body: data == null
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Booking: ${data['booking']?['booking_number'] ?? '-'}'),
                        Text('Telat: ${data['late_checkout_hours'] ?? 0} jam'),
                        Text('Denda: ${_idr((data['late_checkout_penalty'] ?? 0) as num)}'),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                DropdownButtonFormField<String>(
                  initialValue: method,
                  items: const [
                    DropdownMenuItem(value: 'qris', child: Text('QRIS')),
                    DropdownMenuItem(value: 'cash', child: Text('Tunai')),
                  ],
                  onChanged: (v) => setState(() => method = v ?? 'qris'),
                  decoration: const InputDecoration(labelText: 'Metode pembayaran'),
                ),
                const SizedBox(height: 8),
                TextField(
                  controller: paidAmount,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Nominal bayar'),
                ),
                if (method == 'qris') ...[
                  const SizedBox(height: 8),
                  OutlinedButton(
                    onPressed: () async {
                      final pick = await ImagePicker().pickImage(source: ImageSource.gallery);
                      if (pick == null) return;
                      setState(() => proof = File(pick.path));
                    },
                    child: Text(proof == null ? 'Pilih bukti pembayaran denda' : 'File: ${proof!.path.split('\\').last}'),
                  ),
                ],
                const SizedBox(height: 10),
                FilledButton(
                  onPressed: () async {
                    if (method == 'qris' && proof == null) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Untuk QRIS, upload bukti pembayaran dulu')),
                      );
                      return;
                    }

                    final result = await context.read<AppProvider>().payLateCheckoutPenalty(
                          bookingId: widget.bookingId,
                          paymentMethod: method,
                          paidAmount: num.tryParse(paidAmount.text.trim()) ?? 0,
                          paymentProof: proof,
                        );
                    if (!context.mounted) return;
                    if (result == null) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(app.errorMessage ?? 'Pembayaran denda gagal')),
                      );
                      return;
                    }

                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Pembayaran denda berhasil diproses')),
                    );
                    Navigator.pop(context);
                  },
                  child: const Text('Bayar Denda'),
                ),
              ],
            ),
    );
  }

  String _idr(num value) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0)
        .format(value);
  }
}
