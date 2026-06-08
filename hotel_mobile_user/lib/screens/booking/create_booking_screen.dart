import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../models/room_type_model.dart';
import '../../providers/app_provider.dart';

class CreateBookingScreen extends StatefulWidget {
  final RoomTypeModel roomType;

  const CreateBookingScreen({super.key, required this.roomType});

  @override
  State<CreateBookingScreen> createState() => _CreateBookingScreenState();
}

class _CreateBookingScreenState extends State<CreateBookingScreen> {
  DateTime? checkIn;
  DateTime? checkOut;
  TimeOfDay requestedCheckInTime = const TimeOfDay(hour: 14, minute: 0);

  final guestName = TextEditingController();
  final guestEmail = TextEditingController();
  final guestPhone = TextEditingController();

  int totalRooms = 1;
  String paymentMethod = 'qris';

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final user = app.user;
    if (user != null && guestName.text.isEmpty) {
      guestName.text = user.fullName;
      guestEmail.text = user.email;
      guestPhone.text = user.phone ?? '';
    }

    final nights = _nights;
    final subtotal = widget.roomType.pricePerNight * nights * totalRooms;
    final tax = subtotal * 0.1;
    final total = subtotal + tax;
    final requiresQris = total > 500000;
    if (requiresQris && paymentMethod != 'qris') {
      paymentMethod = 'qris';
    }

    return Scaffold(
      appBar: AppBar(title: Text('Pesan ${widget.roomType.name}')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          if (!app.isAuthenticated)
            const Card(
              child: Padding(
                padding: EdgeInsets.all(14),
                child: Text('Login dulu untuk membuat booking.'),
              ),
            ),
          _dateButtons(),
          const SizedBox(height: 12),
          DropdownButtonFormField<int>(
            initialValue: totalRooms,
            items: List.generate(5, (i) => i + 1)
                .map((e) => DropdownMenuItem(value: e, child: Text('$e kamar')))
                .toList(),
            onChanged: (v) => setState(() => totalRooms = v ?? 1),
            decoration: const InputDecoration(labelText: 'Jumlah kamar'),
          ),
          const SizedBox(height: 8),
          DropdownButtonFormField<String>(
            initialValue: paymentMethod,
            decoration: const InputDecoration(labelText: 'Metode pembayaran'),
            items: const [
              DropdownMenuItem(value: 'qris', child: Text('QRIS')),
              DropdownMenuItem(value: 'cash', child: Text('Tunai')),
            ],
            onChanged: requiresQris
                ? null
                : (v) {
                    if (v == null) return;
                    setState(() => paymentMethod = v);
                  },
          ),
          const SizedBox(height: 8),
          if (paymentMethod == 'qris')
            OutlinedButton(
              onPressed: () async {
                final picked = await showTimePicker(
                  context: context,
                  initialTime: requestedCheckInTime,
                );
                if (picked != null) {
                  setState(() => requestedCheckInTime = picked);
                }
              },
              child: Text(
                'Jam check-in pilihan: ${requestedCheckInTime.hour.toString().padLeft(2, '0')}:${requestedCheckInTime.minute.toString().padLeft(2, '0')}',
              ),
            ),
          if (requiresQris)
            const Padding(
              padding: EdgeInsets.only(top: 8),
              child: Text(
                'Total di atas Rp 500.000 wajib QRIS.',
                style: TextStyle(color: Colors.orange, fontWeight: FontWeight.w600),
              ),
            ),
          const SizedBox(height: 8),
          TextField(controller: guestName, decoration: const InputDecoration(labelText: 'Nama tamu')),
          const SizedBox(height: 8),
          TextField(controller: guestEmail, decoration: const InputDecoration(labelText: 'Email')),
          const SizedBox(height: 8),
          TextField(controller: guestPhone, decoration: const InputDecoration(labelText: 'No HP')),
          const SizedBox(height: 12),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Ringkasan', style: Theme.of(context).textTheme.titleMedium),
                  const SizedBox(height: 6),
                  Text('Malam: $nights'),
                  Text('Subtotal: ${_idr(subtotal)}'),
                  Text('Pajak: ${_idr(tax)}'),
                  Text('Total: ${_idr(total)}', style: const TextStyle(fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: !app.isAuthenticated
                ? null
                : () async {
                    if (checkIn == null || checkOut == null || _nights < 1) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Pilih tanggal check-in/check-out yang valid.')),
                      );
                      return;
                    }

                    final ok = await context.read<AppProvider>().createBooking({
                      'room_type_id': widget.roomType.id,
                      'check_in': DateFormat('yyyy-MM-dd').format(checkIn!),
                      'check_out': DateFormat('yyyy-MM-dd').format(checkOut!),
                      'total_rooms': totalRooms,
                      'payment_method': paymentMethod,
                      'requested_checkin_time': paymentMethod == 'qris'
                          ? '${requestedCheckInTime.hour.toString().padLeft(2, '0')}:${requestedCheckInTime.minute.toString().padLeft(2, '0')}'
                          : null,
                    });

                    if (!context.mounted) return;
                    if (ok) {
                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Booking berhasil dibuat')));
                      Navigator.pop(context);
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text(app.errorMessage ?? 'Gagal membuat booking')),
                      );
                    }
                  },
            child: const Text('Buat Booking'),
          ),
        ],
      ),
    );
  }

  Widget _dateButtons() {
    return Column(
      children: [
        OutlinedButton(
          onPressed: () async {
            final date = await showDatePicker(
              context: context,
              firstDate: DateTime.now(),
              lastDate: DateTime.now().add(const Duration(days: 365)),
              initialDate: checkIn ?? DateTime.now(),
            );
            if (date != null) {
              setState(() => checkIn = date);
            }
          },
          child: Text(checkIn == null
              ? 'Tanggal Check-in'
              : DateFormat('dd MMM yyyy').format(checkIn!)),
        ),
        const SizedBox(height: 8),
        OutlinedButton(
          onPressed: () async {
            final first = checkIn ?? DateTime.now();
            final date = await showDatePicker(
              context: context,
              firstDate: first,
              lastDate: DateTime.now().add(const Duration(days: 365)),
              initialDate: checkOut ?? first.add(const Duration(days: 1)),
            );
            if (date != null) {
              setState(() => checkOut = date);
            }
          },
          child: Text(checkOut == null
              ? 'Tanggal Check-out'
              : DateFormat('dd MMM yyyy').format(checkOut!)),
        ),
      ],
    );
  }

  int get _nights {
    if (checkIn == null || checkOut == null) return 0;
    return checkOut!.difference(checkIn!).inDays;
  }

  String _idr(num value) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0)
        .format(value);
  }
}
