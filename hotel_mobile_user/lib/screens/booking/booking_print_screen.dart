import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';

class BookingPrintScreen extends StatefulWidget {
  final String bookingId;
  final bool usePublicEndpoint;

  const BookingPrintScreen({
    super.key,
    required this.bookingId,
    this.usePublicEndpoint = false,
  });

  @override
  State<BookingPrintScreen> createState() => _BookingPrintScreenState();
}

class _BookingPrintScreenState extends State<BookingPrintScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (widget.usePublicEndpoint) {
        await context.read<AppProvider>().openPublicPrintableBooking(widget.bookingId);
      } else {
        await context.read<AppProvider>().openPrintableBooking(widget.bookingId);
        if (!mounted) return;
        await context.read<AppProvider>().openBookingBarcode(widget.bookingId);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final printable = app.selectedPrintableBooking;
    final barcode = app.selectedBookingBarcode;

    return Scaffold(
      appBar: AppBar(title: const Text('Printable Booking')),
      body: printable == null
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
                        Text(printable['booking_number']?.toString() ?? '-', style: Theme.of(context).textTheme.titleLarge),
                        const SizedBox(height: 6),
                        Text('Status booking: ${printable['booking_status'] ?? '-'}'),
                        Text('Status bayar: ${printable['payment_status'] ?? '-'}'),
                        Text('Total: ${_idr((printable['total_amount'] ?? 0) as num)}'),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                if (barcode != null)
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Barcode Number: ${barcode['barcode']?['barcode_number'] ?? '-'}'),
                          Text('Short Path: ${barcode['short_path'] ?? '-'}'),
                          Text('Print Path: ${barcode['print_path'] ?? '-'}'),
                        ],
                      ),
                    ),
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
