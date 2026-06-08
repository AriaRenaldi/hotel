import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../models/room_type_model.dart';
import '../../providers/app_provider.dart';
import '../booking/create_booking_screen.dart';

class RoomsScreen extends StatefulWidget {
  const RoomsScreen({super.key});

  @override
  State<RoomsScreen> createState() => _RoomsScreenState();
}

class _RoomsScreenState extends State<RoomsScreen> {
  DateTime? checkIn;
  DateTime? checkOut;
  int totalRooms = 1;
  String availabilityMessage = '';

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final format = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return SafeArea(
      child: RefreshIndicator(
        onRefresh: () => context.read<AppProvider>().loadPublicData(),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text('Temukan Kamar Terbaik', style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 8),
            const Text('Booking cepat, langsung dari handphone.'),
            const SizedBox(height: 16),
            _buildAvailabilityCard(context),
            if (availabilityMessage.isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(availabilityMessage, style: const TextStyle(fontWeight: FontWeight.w600)),
            ],
            const SizedBox(height: 12),
            ...app.roomTypes.map((room) => _roomCard(context, room, format)),
          ],
        ),
      ),
    );
  }

  Widget _buildAvailabilityCard(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Cek Ketersediaan', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () async {
                      final date = await showDatePicker(
                        context: context,
                        firstDate: DateTime.now(),
                        lastDate: DateTime.now().add(const Duration(days: 365)),
                        initialDate: checkIn ?? DateTime.now(),
                      );
                      if (date != null) setState(() => checkIn = date);
                    },
                    child: Text(checkIn == null ? 'Check-in' : DateFormat('dd MMM').format(checkIn!)),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: OutlinedButton(
                    onPressed: () async {
                      final baseDate = checkIn ?? DateTime.now();
                      final date = await showDatePicker(
                        context: context,
                        firstDate: baseDate,
                        lastDate: DateTime.now().add(const Duration(days: 365)),
                        initialDate: checkOut ?? baseDate.add(const Duration(days: 1)),
                      );
                      if (date != null) setState(() => checkOut = date);
                    },
                    child: Text(checkOut == null ? 'Check-out' : DateFormat('dd MMM').format(checkOut!)),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            DropdownButtonFormField<int>(
              initialValue: totalRooms,
              decoration: const InputDecoration(labelText: 'Jumlah kamar'),
              items: List.generate(5, (i) => i + 1)
                  .map((e) => DropdownMenuItem(value: e, child: Text('$e kamar')))
                  .toList(),
              onChanged: (v) => setState(() => totalRooms = v ?? 1),
            ),
            const SizedBox(height: 10),
            SizedBox(
              width: double.infinity,
              child: FilledButton(
                onPressed: () async {
                  if (checkIn == null || checkOut == null) {
                    setState(() => availabilityMessage = 'Pilih tanggal check-in dan check-out dulu.');
                    return;
                  }
                  final result = await context.read<AppProvider>().checkAvailability(
                        checkIn: DateFormat('yyyy-MM-dd').format(checkIn!),
                        checkOut: DateFormat('yyyy-MM-dd').format(checkOut!),
                        roomTypeId: null,
                        totalRooms: totalRooms,
                      );
                  if (!context.mounted) return;
                  setState(() => availabilityMessage = result?['message']?.toString() ?? 'Tidak ada respons.');
                },
                child: const Text('Cek Sekarang'),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _roomCard(BuildContext context, RoomTypeModel room, NumberFormat format) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(room.name, style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 4),
            Text(room.description ?? '-'),
            const SizedBox(height: 8),
            Wrap(
              spacing: 6,
              children: room.facilities.take(4).map((f) => Chip(label: Text(f))).toList(),
            ),
            const SizedBox(height: 10),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(format.format(room.pricePerNight), style: Theme.of(context).textTheme.titleMedium),
                FilledButton(
                  onPressed: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => CreateBookingScreen(roomType: room)),
                  ),
                  child: const Text('Pesan'),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }
}
