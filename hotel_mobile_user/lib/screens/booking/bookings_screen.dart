import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../providers/app_provider.dart';
import 'booking_detail_screen.dart';

class BookingsScreen extends StatelessWidget {
  const BookingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppProvider>();
    final format = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return SafeArea(
      child: RefreshIndicator(
        onRefresh: () => context.read<AppProvider>().loadBookings(),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text('Booking Saya', style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 12),
            if (!app.isAuthenticated)
              const Card(child: Padding(padding: EdgeInsets.all(16), child: Text('Login dulu untuk melihat booking.'))),
            ...app.bookings.map((booking) {
              return Card(
                margin: const EdgeInsets.only(bottom: 10),
                child: ListTile(
                  title: Text(booking.bookingNumber),
                  subtitle: Text('${booking.roomTypeName ?? '-'} | ${booking.checkInDate} - ${booking.checkOutDate}'),
                  trailing: Text(format.format(booking.totalAmount)),
                  onTap: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => BookingDetailScreen(bookingId: booking.id.toString())),
                  ),
                ),
              );
            }),
          ],
        ),
      ),
    );
  }
}
