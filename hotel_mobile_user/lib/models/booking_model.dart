class BookingModel {
  final int id;
  final String bookingNumber;
  final String checkInDate;
  final String checkOutDate;
  final num totalAmount;
  final String bookingStatus;
  final String? paymentStatus;
  final String? roomTypeName;

  BookingModel({
    required this.id,
    required this.bookingNumber,
    required this.checkInDate,
    required this.checkOutDate,
    required this.totalAmount,
    required this.bookingStatus,
    this.paymentStatus,
    this.roomTypeName,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    final roomType = json['room_type'];
    return BookingModel(
      id: json['id'] as int,
      bookingNumber: (json['booking_number'] ?? '-') as String,
      checkInDate: (json['check_in_date'] ?? '-') as String,
      checkOutDate: (json['check_out_date'] ?? '-') as String,
      totalAmount: (json['total_amount'] ?? 0) as num,
      bookingStatus: (json['booking_status'] ?? '-') as String,
      paymentStatus: json['payment_status'] as String?,
      roomTypeName: roomType is Map<String, dynamic> ? roomType['name'] as String? : null,
    );
  }
}
