import 'dart:io';

import 'package:dio/dio.dart';

import '../models/booking_model.dart';
import '../models/room_type_model.dart';
import '../models/user_model.dart';
import 'api_client.dart';

class AppRepository {
  final ApiClient client;

  AppRepository(this.client);

  Future<(String, UserModel)> login(String email, String password) async {
    final response = await client.dio.post('/auth/login', data: {
      'email': email,
      'password': password,
    });

    final data = Map<String, dynamic>.from(response.data['data'] as Map);
    final token = data['token'] as String;
    final user = UserModel.fromJson(Map<String, dynamic>.from(data['user'] as Map));
    client.setToken(token);
    return (token, user);
  }

  Future<void> register(Map<String, dynamic> payload) async {
    await client.dio.post('/auth/register', data: payload);
  }

  Future<void> verifyOtp({
    required String email,
    required String otpCode,
  }) async {
    await client.dio.post('/auth/verify-otp', data: {
      'email': email,
      'otp_code': otpCode,
    });
  }

  Future<void> resendOtp({
    required String email,
    required String type,
    required String otpChannel,
  }) async {
    await client.dio.post('/auth/resend-otp', data: {
      'email': email,
      'type': type,
      'otp_channel': otpChannel,
    });
  }

  Future<void> forgotPassword({
    required String email,
    String otpChannel = 'gmail',
  }) async {
    await client.dio.post('/auth/forgot-password', data: {
      'email': email,
      'otp_channel': otpChannel,
    });
  }

  Future<void> resetPassword({
    required String email,
    required String otpCode,
    required String password,
  }) async {
    await client.dio.post('/auth/reset-password', data: {
      'email': email,
      'otp_code': otpCode,
      'password': password,
    });
  }

  Future<List<RoomTypeModel>> getRoomTypes() async {
    final response = await client.dio.get('/hotel/room-types');
    final list = response.data['data'] as List<dynamic>;
    return list
        .map((e) => RoomTypeModel.fromJson(Map<String, dynamic>.from(e as Map)))
        .toList();
  }

  Future<List<dynamic>> getFacilities() async {
    final response = await client.dio.get('/hotel/facilities');
    return response.data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> checkAvailability({
    required String checkIn,
    required String checkOut,
    required int? roomTypeId,
    required int totalRooms,
  }) async {
    final response = await client.dio.post('/hotel/check-availability', data: {
      'check_in': checkIn,
      'check_out': checkOut,
      'room_type_id': roomTypeId,
      'total_rooms': totalRooms,
    });
    return Map<String, dynamic>.from(response.data as Map);
  }

  Future<void> createBooking(Map<String, dynamic> payload) async {
    await client.dio.post('/guest/bookings', data: payload);
  }

  Future<List<BookingModel>> getMyBookings() async {
    final response = await client.dio.get('/guest/bookings/my-bookings');
    final list = response.data['data'] as List<dynamic>;
    return list
        .map((e) => BookingModel.fromJson(Map<String, dynamic>.from(e as Map)))
        .toList();
  }

  Future<Map<String, dynamic>> checkBookingPublic({
    required String bookingNumber,
    required String email,
  }) async {
    final response = await client.dio.post('/hotel/check-booking', data: {
      'booking_number': bookingNumber,
      'email': email,
    });
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<Map<String, dynamic>> getBookingDetail(String bookingId) async {
    final response = await client.dio.get('/guest/bookings/$bookingId');
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<Map<String, dynamic>> getPrintableBooking(String bookingId) async {
    final response = await client.dio.get('/guest/bookings/$bookingId/print');
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<Map<String, dynamic>> getBookingBarcode(String bookingId) async {
    final response = await client.dio.get('/guest/bookings/$bookingId/barcode');
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<Map<String, dynamic>> getPublicPrintableBooking(String bookingId) async {
    final response = await client.dio.get('/hotel/bookings/$bookingId/print');
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<void> cancelBooking(String bookingId) async {
    await client.dio.delete('/guest/bookings/$bookingId');
  }

  Future<Map<String, dynamic>> guestCheckOut(String bookingId) async {
    final response = await client.dio.post('/guest/bookings/$bookingId/check-out');
    final data = response.data['data'];
    if (data is Map<String, dynamic>) {
      return data;
    }
    return <String, dynamic>{};
  }

  Future<void> uploadPaymentProof({
    required String bookingId,
    required File file,
    required num paidAmount,
  }) async {
    final formData = FormData.fromMap({
      'paid_amount': paidAmount,
      'payment_proof': await MultipartFile.fromFile(file.path),
    });
    await client.dio.post('/guest/bookings/$bookingId/upload-proof', data: formData);
  }

  Future<void> uploadCheckInReceipt({
    required String bookingId,
    required File file,
  }) async {
    final formData = FormData.fromMap({
      'checkin_receipt': await MultipartFile.fromFile(file.path),
    });

    await client.dio.post('/guest/bookings/$bookingId/upload-checkin-receipt', data: formData);
  }

  Future<Map<String, dynamic>> getLateCheckoutPenaltyDetail(String bookingId) async {
    final response =
        await client.dio.get('/guest/bookings/$bookingId/late-checkout-penalty');
    return Map<String, dynamic>.from(response.data['data'] as Map);
  }

  Future<Map<String, dynamic>> payLateCheckoutPenalty({
    required String bookingId,
    required String paymentMethod,
    required num paidAmount,
    File? paymentProof,
  }) async {
    final payload = <String, dynamic>{
      'payment_method': paymentMethod,
      'paid_amount': paidAmount,
    };

    if (paymentProof != null) {
      payload['payment_proof'] = await MultipartFile.fromFile(paymentProof.path);
    }

    final response = await client.dio.post(
      '/guest/bookings/$bookingId/late-checkout-penalty/pay',
      data: FormData.fromMap(payload),
    );

    final data = response.data['data'];
    if (data is Map<String, dynamic>) {
      return data;
    }

    return <String, dynamic>{};
  }

  Future<UserModel> getProfile() async {
    final response = await client.dio.get('/profile');
    final data = response.data['data'];
    final user = data is Map<String, dynamic> && data['user'] != null
        ? data['user'] as Map<String, dynamic>
        : Map<String, dynamic>.from(data as Map);
    return UserModel.fromJson(user);
  }

  Future<void> updateProfile(Map<String, dynamic> payload) async {
    await client.dio.put('/profile', data: payload);
  }

  Future<UserModel> uploadProfilePhoto(File file) async {
    final formData = FormData.fromMap({
      'photo': await MultipartFile.fromFile(file.path),
    });

    final response = await client.dio.post('/profile/photo', data: formData);
    final user = Map<String, dynamic>.from(response.data['data']['user'] as Map);
    return UserModel.fromJson(user);
  }

  Future<UserModel> deleteProfilePhoto() async {
    final response = await client.dio.delete('/profile/photo');
    final user = Map<String, dynamic>.from(response.data['data']['user'] as Map);
    return UserModel.fromJson(user);
  }

  Future<void> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    await client.dio.post('/profile/change-password', data: {
      'current_password': currentPassword,
      'new_password': newPassword,
      'new_password_confirmation': newPassword,
    });
  }
}
