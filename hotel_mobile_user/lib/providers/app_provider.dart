import 'dart:io';

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../models/booking_model.dart';
import '../models/room_type_model.dart';
import '../models/user_model.dart';
import '../services/api_client.dart';
import '../services/app_repository.dart';

class AppProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();
  late final AppRepository _repo = AppRepository(_client);

  UserModel? user;
  String? token;
  bool isLoading = false;
  bool initialized = false;
  String? errorMessage;

  List<RoomTypeModel> roomTypes = [];
  List<dynamic> facilities = [];
  List<BookingModel> bookings = [];
  Map<String, dynamic>? selectedBooking;
  Map<String, dynamic>? selectedPrintableBooking;
  Map<String, dynamic>? selectedBookingBarcode;
  Map<String, dynamic>? selectedPenaltyDetail;

  bool get isAuthenticated => token != null && token!.isNotEmpty;

  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    token = prefs.getString('auth_token');
    if (token != null) {
      _client.setToken(token);
      try {
        user = await _repo.getProfile();
      } catch (_) {
        await logout();
      }
    }
    await loadPublicData();
    if (isAuthenticated) {
      await loadBookings();
    }
    initialized = true;
    notifyListeners();
  }

  Future<void> loadPublicData() async {
    try {
      roomTypes = await _repo.getRoomTypes();
      facilities = await _repo.getFacilities();
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat data hotel');
      notifyListeners();
    }
  }

  Future<bool> login(String email, String password) async {
    isLoading = true;
    errorMessage = null;
    notifyListeners();
    try {
      final (tokenValue, userValue) = await _repo.login(email, password);
      token = tokenValue;
      user = userValue;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', tokenValue);
      await loadBookings();
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Login gagal');
      return false;
    } finally {
      isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> register(Map<String, dynamic> payload) async {
    isLoading = true;
    errorMessage = null;
    notifyListeners();
    try {
      await _repo.register(payload);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Registrasi gagal');
      return false;
    } finally {
      isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> verifyOtp(String email, String otpCode) async {
    isLoading = true;
    errorMessage = null;
    notifyListeners();
    try {
      await _repo.verifyOtp(email: email, otpCode: otpCode);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Verifikasi OTP gagal');
      return false;
    } finally {
      isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> resendOtp({
    required String email,
    required String type,
    required String otpChannel,
  }) async {
    try {
      await _repo.resendOtp(email: email, type: type, otpChannel: otpChannel);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Kirim ulang OTP gagal');
      notifyListeners();
      return false;
    }
  }

  Future<bool> forgotPassword(String email) async {
    try {
      await _repo.forgotPassword(email: email);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal mengirim OTP reset');
      notifyListeners();
      return false;
    }
  }

  Future<bool> resetPassword({
    required String email,
    required String otpCode,
    required String newPassword,
  }) async {
    try {
      await _repo.resetPassword(email: email, otpCode: otpCode, password: newPassword);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Reset password gagal');
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    token = null;
    user = null;
    bookings = [];
    selectedBooking = null;
    selectedPrintableBooking = null;
    selectedBookingBarcode = null;
    selectedPenaltyDetail = null;
    _client.setToken(null);
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    notifyListeners();
  }

  Future<void> loadBookings() async {
    if (!isAuthenticated) return;
    try {
      bookings = await _repo.getMyBookings();
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat booking');
      notifyListeners();
    }
  }

  Future<bool> createBooking(Map<String, dynamic> payload) async {
    try {
      await _repo.createBooking(payload);
      await loadBookings();
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal membuat booking');
      notifyListeners();
      return false;
    }
  }

  Future<Map<String, dynamic>?> checkAvailability({
    required String checkIn,
    required String checkOut,
    required int? roomTypeId,
    required int totalRooms,
  }) async {
    try {
      return await _repo.checkAvailability(
        checkIn: checkIn,
        checkOut: checkOut,
        roomTypeId: roomTypeId,
        totalRooms: totalRooms,
      );
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal cek ketersediaan');
      notifyListeners();
      return null;
    }
  }

  Future<void> openBookingDetail(String bookingId) async {
    try {
      selectedBooking = await _repo.getBookingDetail(bookingId);
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat detail booking');
      notifyListeners();
    }
  }

  Future<void> openPrintableBooking(String bookingId) async {
    try {
      selectedPrintableBooking = await _repo.getPrintableBooking(bookingId);
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat data print booking');
      notifyListeners();
    }
  }

  Future<void> openPublicPrintableBooking(String bookingId) async {
    try {
      selectedPrintableBooking = await _repo.getPublicPrintableBooking(bookingId);
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat data print publik');
      notifyListeners();
    }
  }

  Future<void> openBookingBarcode(String bookingId) async {
    try {
      selectedBookingBarcode = await _repo.getBookingBarcode(bookingId);
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal memuat barcode booking');
      notifyListeners();
    }
  }

  Future<bool> uploadPaymentProof(String bookingId, File file, num paidAmount) async {
    try {
      await _repo.uploadPaymentProof(
        bookingId: bookingId,
        file: file,
        paidAmount: paidAmount,
      );
      await openBookingDetail(bookingId);
      await loadBookings();
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal upload bukti pembayaran');
      notifyListeners();
      return false;
    }
  }

  Future<bool> uploadCheckInReceipt(String bookingId, File file) async {
    try {
      await _repo.uploadCheckInReceipt(bookingId: bookingId, file: file);
      await openBookingDetail(bookingId);
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal upload struk check-in');
      notifyListeners();
      return false;
    }
  }

  Future<void> cancelBooking(String bookingId) async {
    try {
      await _repo.cancelBooking(bookingId);
      await loadBookings();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal membatalkan booking');
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>?> checkOut(String bookingId) async {
    try {
      final result = await _repo.guestCheckOut(bookingId);
      await openBookingDetail(bookingId);
      await loadBookings();
      return result;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal check-out');
      notifyListeners();
      return null;
    }
  }

  Future<void> openLateCheckoutPenaltyDetail(String bookingId) async {
    try {
      selectedPenaltyDetail = await _repo.getLateCheckoutPenaltyDetail(bookingId);
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal mengambil detail denda checkout');
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>?> payLateCheckoutPenalty({
    required String bookingId,
    required String paymentMethod,
    required num paidAmount,
    File? paymentProof,
  }) async {
    try {
      final result = await _repo.payLateCheckoutPenalty(
        bookingId: bookingId,
        paymentMethod: paymentMethod,
        paidAmount: paidAmount,
        paymentProof: paymentProof,
      );
      await openBookingDetail(bookingId);
      await loadBookings();
      return result;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal membayar denda checkout');
      notifyListeners();
      return null;
    }
  }

  Future<Map<String, dynamic>?> checkBookingPublic({
    required String bookingNumber,
    required String email,
  }) async {
    try {
      return await _repo.checkBookingPublic(bookingNumber: bookingNumber, email: email);
    } catch (e) {
      errorMessage = _resolveError(e, 'Cek booking gagal');
      notifyListeners();
      return null;
    }
  }

  Future<void> saveProfile({
    required String fullName,
    required String phone,
    required String address,
  }) async {
    try {
      await _repo.updateProfile({
        'full_name': fullName,
        'phone': phone,
        'address': address,
      });
      user = await _repo.getProfile();
      notifyListeners();
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal update profil');
      notifyListeners();
    }
  }

  Future<bool> uploadProfilePhoto(File file) async {
    try {
      user = await _repo.uploadProfilePhoto(file);
      notifyListeners();
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal upload foto profil');
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteProfilePhoto() async {
    try {
      user = await _repo.deleteProfilePhoto();
      notifyListeners();
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal hapus foto profil');
      notifyListeners();
      return false;
    }
  }

  Future<bool> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    try {
      await _repo.changePassword(
        currentPassword: currentPassword,
        newPassword: newPassword,
      );
      return true;
    } catch (e) {
      errorMessage = _resolveError(e, 'Gagal ubah password');
      notifyListeners();
      return false;
    }
  }

  String _resolveError(Object e, String fallback) {
    if (e is DioException) {
      final data = e.response?.data;
      if (data is Map<String, dynamic>) {
        if (data['message'] is String) return data['message'] as String;
        final errors = data['errors'];
        if (errors is Map<String, dynamic> && errors.isNotEmpty) {
          final first = errors.values.first;
          if (first is List && first.isNotEmpty) {
            return first.first.toString();
          }
          return first.toString();
        }
      }
    }
    return fallback;
  }
}
