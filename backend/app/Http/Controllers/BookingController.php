<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingBarcode;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    private const LATE_CHECKOUT_PENALTY_PER_HOUR = 75000;
    private const STANDARD_CHECKOUT_HOUR = 12;
    private const LATE_CHECKOUT_GRACE_MINUTES = 0;
    private const HOTEL_TIMEZONE = 'Asia/Jakarta';
    private const MAX_CASH_BOOKING_AMOUNT = 500000;

    /**
     * Guest: Create booking
     */
    public function createBooking(Request $request)
    {
        try {
            // Backward compatibility for legacy frontend payloads.
            $normalizedPaymentMethod = strtolower((string) $request->input('payment_method', 'qris'));
            if ($normalizedPaymentMethod === 'transfer') {
                $normalizedPaymentMethod = 'qris';
            }

            $request->merge([
                'check_in' => $request->input('check_in', $request->input('check_in_date')),
                'check_out' => $request->input('check_out', $request->input('check_out_date')),
                'payment_method' => $normalizedPaymentMethod,
                'requested_checkin_time' => $request->input('requested_checkin_time'),
            ]);

            $validator = Validator::make($request->all(), [
                'room_type_id' => 'required|exists:room_types,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'total_rooms' => 'required|integer|min:1',
                'payment_method' => 'nullable|in:qris,cash',
                'requested_checkin_time' => 'nullable|date_format:H:i'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (is_array($request->input('room_type_id'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dalam satu pemesanan hanya diperbolehkan satu tipe kamar.',
                    'errors' => [
                        'room_type_id' => ['Pilih satu tipe kamar untuk satu kali pemesanan.']
                    ]
                ], 422);
            }

            $user = $request->user();

            // Get room type
            $roomType = RoomType::find($request->room_type_id);

            // Calculate nights
            $checkIn = new \DateTime($request->check_in);
            $checkOut = new \DateTime($request->check_out);
            $totalNights = $checkIn->diff($checkOut)->days;

            // Calculate total amount
            $subtotal = $roomType->price_per_night * $totalNights * $request->total_rooms;
            $tax = $subtotal * 0.1;
            $totalAmount = $subtotal + $tax;
            $selectedPaymentMethod = $request->payment_method ?: 'qris';

            if ($totalAmount > self::MAX_CASH_BOOKING_AMOUNT && $selectedPaymentMethod !== 'qris') {
                return response()->json([
                    'success' => false,
                    'message' => 'Total pemesanan di atas Rp 500.000 wajib menggunakan pembayaran QRIS.',
                    'errors' => [
                        'payment_method' => [
                            'Metode tunai tidak tersedia untuk total pemesanan di atas Rp 500.000.'
                        ]
                    ]
                ], 422);
            }

            $isCashPayment = $selectedPaymentMethod === 'cash';
            $requestedCheckInTime = $selectedPaymentMethod === 'qris'
                ? ($request->input('requested_checkin_time') ?: null)
                : null;

            if ($selectedPaymentMethod === 'qris' && !$requestedCheckInTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Untuk metode QRIS, pilih jam check-in terlebih dahulu.',
                    'errors' => [
                        'requested_checkin_time' => [
                            'Jam check-in wajib diisi untuk pembayaran QRIS.'
                        ]
                    ]
                ], 422);
            }

            $initialPaymentStatus = $isCashPayment ? 'verified' : 'pending';

            // Check available rooms
            $availableRooms = $this->checkRoomAvailability($request->room_type_id, $request->check_in, $request->check_out);

            if (count($availableRooms) < $request->total_rooms) {
                return response()->json([
                    'success' => false,
                    'message' => "Kamar yang tersedia hanya " . count($availableRooms) . " unit.",
                ], 400);
            }

            DB::beginTransaction();

            // Generate booking number
            $bookingNumber = 'BK' . date('Ymd') . rand(1000, 9999);

            // Create booking
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'guest_id' => $user->id,
                'check_in_date' => $request->check_in,
                'requested_checkin_time' => $requestedCheckInTime,
                'check_out_date' => $request->check_out,
                'actual_check_in_at' => null,
                'total_rooms' => $request->total_rooms,
                'total_nights' => $totalNights,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'payment_method' => $selectedPaymentMethod,
                'payment_status' => $initialPaymentStatus,
                'booking_status' => 'confirmed',
            ]);

            // Assign rooms to booking
            $assignedRoomIds = [];
            for ($i = 0; $i < $request->total_rooms; $i++) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'room_id' => $availableRooms[$i]->id,
                    'price_per_night' => $roomType->price_per_night,
                ]);
                $assignedRoomIds[] = $availableRooms[$i]->id;
            }

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_type' => 'booking',
                'amount' => $totalAmount,
                'payment_method' => $selectedPaymentMethod,
                'status' => $isCashPayment ? 'verified' : 'pending',
                'verified_at' => $isCashPayment ? now() : null,
            ]);

            $barcode = $this->createOrRefreshBookingBarcode($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isCashPayment
                    ? 'Pemesanan tunai berhasil dibuat. Silakan upload struk booking saat check-in agar dapat disetujui resepsionis.'
                    : 'Pemesanan berhasil dibuat. Silakan lakukan pembayaran QRIS lalu tunggu persetujuan pembayaran dari resepsionis.',
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'total_amount' => $totalAmount,
                    'payment_method' => $selectedPaymentMethod,
                    'requested_checkin_time' => $requestedCheckInTime,
                    'booking_status' => $booking->booking_status,
                    'auto_checked_in' => false,
                    'barcode' => $barcode,
                    'allowed_payment_methods' => $totalAmount > self::MAX_CASH_BOOKING_AMOUNT
                        ? ['qris']
                        : ['qris', 'cash'],
                    'must_upload_payment_proof' => !$isCashPayment,
                    'requires_receptionist_payment_approval' => $selectedPaymentMethod === 'qris',
                    'requires_receptionist_checkin_approval' => true,
                    'next_step' => [
                        'path' => $isCashPayment ? "/guest/booking/{$booking->id}" : "/guest/payment/{$booking->id}",
                        'type' => $isCashPayment ? 'review_booking_status' : 'upload_qris_proof'
                    ],
                    'payment_instruction' => $isCashPayment
                        ? 'Pembayaran tunai tidak perlu upload bukti pembayaran. Saat datang check-in, silakan upload struk booking agar resepsionis dapat menyetujui check-in.'
                        : 'Silakan scan QRIS dan upload bukti pembayaran dengan nominal yang sama persis dengan total tagihan. Pembayaran akan diverifikasi resepsionis sebelum check-in dapat diproses.'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Upload payment proof
     */
    public function uploadPaymentProof(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::query(),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->guest_id != $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pemesanan ini'
                ], 403);
            }

            if ($booking->payment_method !== 'qris') {
                return response()->json([
                    'success' => true,
                    'message' => 'Metode pembayaran booking ini tidak memerlukan upload bukti.',
                    'data' => [
                        'payment_method' => $booking->payment_method,
                        'must_upload_payment_proof' => false,
                    ]
                ]);
            }

            // Backward compatibility for legacy frontend form field names.
            $proofFile = $request->file('payment_proof')
                ?? $request->file('proof')
                ?? $request->file('file')
                ?? $request->file('image');

            if ($proofFile) {
                $request->files->set('payment_proof', $proofFile);
            }

            $normalizedPaidAmount = $request->input('paid_amount')
                ?? $request->input('amount')
                ?? $request->input('total_paid')
                ?? $booking->total_amount;

            $request->merge([
                'paid_amount' => $normalizedPaidAmount,
            ]);

            $validator = Validator::make($request->all(), [
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'paid_amount' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $paidAmount = $this->normalizeMoneyAmount($request->paid_amount);
            $requiredAmount = $this->normalizeMoneyAmount($booking->total_amount);

            if (abs($paidAmount - $requiredAmount) > 0.00001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal pembayaran pada bukti harus sama dengan total tagihan.',
                    'errors' => [
                        'paid_amount' => [
                            'Nominal bayar harus sama dengan total tagihan.'
                        ]
                    ]
                ], 422);
            }

            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            $booking->update([
                'payment_proof' => $proofPath,
                'payment_status' => 'paid'
            ]);

            // Update payment record
            $payment = Payment::where('booking_id', $booking->id)
                ->where('payment_type', 'booking')
                ->latest('id')
                ->first();
            if ($payment) {
                $payment->update([
                    'amount' => $paidAmount,
                    'proof_image' => $proofPath,
                    'status' => 'pending',
                    'verified_at' => null,
                    'verified_by' => null,
                    'notes' => 'Guest upload bukti QRIS. Menunggu persetujuan pembayaran dari resepsionis.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload. Pembayaran menunggu persetujuan resepsionis.',
                'data' => [
                    'proof_url' => url('storage/' . $proofPath),
                    'auto_checked_in' => false,
                    'requires_receptionist_payment_approval' => true,
                    'requires_receptionist_checkin_approval' => false,
                    'booking_status' => $booking->fresh()->booking_status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload bukti pembayaran gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Get my bookings
     */
    public function getMyBookings(Request $request)
    {
        try {
            $bookings = Booking::with(['bookingDetails.room.roomType', 'barcode'])
                ->where('guest_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $bookings = $this->refreshAutoCheckInForCollection(
                $bookings,
                ['bookingDetails.room.roomType', 'barcode']
            );

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Get booking detail (own bookings only)
     */
    public function getBookingDetail(Request $request, $bookingId)
    {
        try {
            $user = $request->user();
            $query = Booking::with(['bookingDetails.room.roomType', 'payments', 'guest', 'barcode']);

            // If guest, only show own bookings
            if ($user->role === 'guest') {
                $query->where('guest_id', $user->id);
            }

            $booking = $this->findBookingByIdentifier($query, $bookingId)->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            $booking = $this->refreshAutoCheckInForBooking(
                $booking,
                ['bookingDetails.room.roomType', 'payments', 'guest', 'barcode']
            );

            $this->createOrRefreshBookingBarcode($booking);
            $booking->load('barcode');

            // Get room type from first booking detail
            $roomType = $booking->bookingDetails->first()?->room?->roomType;

            $bookingData = $booking->toArray();
            $bookingData['room_type'] = $roomType;
            $bookingData['barcode'] = $booking->barcode;

            return response()->json([
                'success' => true,
                'data' => $bookingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Upload printed booking receipt image for receptionist check-in approval.
     */
    public function uploadCheckInReceipt(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::query(),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->guest_id != $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pemesanan ini'
                ], 403);
            }

            if ($booking->payment_status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Struk check-in hanya dapat diupload setelah pembayaran terverifikasi.'
                ], 422);
            }

            if ($booking->booking_status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload struk check-in hanya dapat dilakukan sebelum proses check-in.'
                ], 422);
            }

            $receiptFile = $request->file('checkin_receipt')
                ?? $request->file('receipt')
                ?? $request->file('payment_receipt')
                ?? $request->file('image');

            if ($receiptFile) {
                $request->files->set('checkin_receipt', $receiptFile);
            }

            $validator = Validator::make($request->all(), [
                'checkin_receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $receiptPath = $request->file('checkin_receipt')->store('checkin-receipts', 'public');

            $booking->update([
                'checkin_receipt_proof' => $receiptPath,
                'checkin_receipt_uploaded_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Struk check-in berhasil diupload. Silakan tunggu persetujuan check-in dari resepsionis.',
                'data' => [
                    'checkin_receipt_proof_url' => url('storage/' . $receiptPath),
                    'checkin_receipt_uploaded_at' => $booking->fresh()->checkin_receipt_uploaded_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload struk check-in gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Get barcode data for booking.
     */
    public function getBookingBarcode(Request $request, $bookingId)
    {
        try {
            $query = Booking::query();
            $user = $request->user();

            if ($user && $user->role === 'guest') {
                $query->where('guest_id', $user->id);
            }

            $booking = $this->findBookingByIdentifier($query, $bookingId)->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            $barcode = $this->createOrRefreshBookingBarcode($booking);

            return response()->json([
                'success' => true,
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'barcode' => $barcode,
                    'print_path' => '/guest/booking/print/' . $booking->id,
                    'short_path' => '/p/' . rawurlencode((string) $booking->id),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil barcode pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist/Admin: Get today's bookings
     */
    public function getTodayBookings(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');

            $bookings = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments', 'barcode'])
                ->where(function ($query) use ($today) {
                    $query->whereDate('check_in_date', $today)
                          ->orWhereDate('check_out_date', $today)
                          ->orWhereDate('actual_check_out_at', $today);
                });

            if ($request->user()?->role === 'receptionist') {
                $this->applyReceptionistVisibilityScope($bookings, (int) $request->user()->id);
            }

            $bookings = $bookings
                ->orderBy('created_at', 'desc')
                ->get();

            $bookings = $this->refreshAutoCheckInForCollection(
                $bookings,
                ['guest', 'bookingDetails.room.roomType', 'payments', 'barcode']
            );

            $checkIns = $bookings->filter(fn($b) => $b->check_in_date->format('Y-m-d') === $today);
            $checkOuts = $bookings->filter(function ($booking) use ($today) {
                if ($booking->booking_status === 'checked_out' && $booking->actual_check_out_at) {
                    return $booking->actual_check_out_at->format('Y-m-d') === $today;
                }

                return $booking->check_out_date->format('Y-m-d') === $today;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $today,
                    'total' => $bookings->count(),
                    'check_ins' => $checkIns->values(),
                    'check_outs' => $checkOuts->values(),
                    'all' => $bookings,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemesanan hari ini: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Print booking
     */
    public function printBooking(Request $request, $bookingId)
    {
        try {
            $query = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments.verifier', 'barcode']);
            $user = $request->user();

            if ($user && $user->role === 'guest') {
                $query->where('guest_id', $user->id);
            }

            $booking = $this->findBookingByIdentifier($query, $bookingId)->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->payment_status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Struk hanya dapat dicetak setelah pembayaran terverifikasi (Lunas/Bayar di Tempat).'
                ], 422);
            }

            $this->createOrRefreshBookingBarcode($booking);
            $booking->load('barcode');

            $roomType = $booking->bookingDetails->first()?->room?->roomType;
            $bookingData = $booking->toArray();
            $bookingData['room_type'] = $roomType;
            $bookingData['barcode'] = $booking->barcode;
            $bookingData['late_checkout_penalty_per_hour'] = self::LATE_CHECKOUT_PENALTY_PER_HOUR;
            $bookingData['grace_minutes'] = self::LATE_CHECKOUT_GRACE_MINUTES;

            if ($booking->booking_status === 'checked_in') {
                $lateCheckoutSnapshot = $this->buildLateCheckoutSnapshot($booking);
                $bookingData = array_merge($bookingData, $lateCheckoutSnapshot);
            }

            return response()->json([
                'success' => true,
                'data' => $bookingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Check booking by number and email
     */
    public function checkBooking(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'booking_number' => 'required|string',
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $booking = Booking::with(['bookingDetails.room.roomType', 'payments', 'guest', 'barcode'])
                ->where('booking_number', $request->booking_number)
                ->whereHas('guest', function($q) use ($request) {
                    $q->where('email', $request->email);
                })
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            $booking = $this->refreshAutoCheckInForBooking(
                $booking,
                ['bookingDetails.room.roomType', 'payments', 'guest', 'barcode']
            );

            $this->createOrRefreshBookingBarcode($booking);
            $booking->load('barcode');

            return response()->json([
                'success' => true,
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get all bookings with filters
     */
    public function getAllBookings(Request $request)
    {
        try {
            $query = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments.verifier', 'barcode']);

            if ($request->user()?->role === 'receptionist') {
                $this->applyReceptionistVisibilityScope($query, (int) $request->user()->id);
            }

            // Filter by check-in date
            if ($request->check_in_date) {
                $query->where('check_in_date', $request->check_in_date);
            }

            // Filter by guest name
            if ($request->guest_name) {
                $query->whereHas('guest', function($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->guest_name . '%');
                });
            }

            // Filter by booking status
            if ($request->booking_status) {
                $query->where('booking_status', $request->booking_status);
            }

            // Filter by payment status
            if ($request->payment_status) {
                $query->where('payment_status', $request->payment_status);
            }

            $bookings = $query->orderBy('created_at', 'desc')->get();

            $bookings = $this->refreshAutoCheckInForCollection(
                $bookings,
                ['guest', 'bookingDetails.room.roomType', 'payments', 'barcode']
            );

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Update booking status
     */
    public function updateBookingStatus(Request $request, $bookingId)
    {
        try {
            if ($request->user()?->role === 'receptionist') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status booking dikelola otomatis oleh sistem dan tidak dapat diubah manual oleh resepsionis.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'booking_status' => 'required|in:confirmed,checked_in,cancelled',
                'payment_status' => 'nullable|in:pending,paid,verified,cancelled'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            $updateData = ['booking_status' => $request->booking_status];
            
            if ($request->payment_status) {
                $updateData['payment_status'] = $request->payment_status;
            }

            if ($request->booking_status === 'checked_in' && $booking->payment_status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in hanya bisa dilakukan setelah pembayaran terverifikasi.'
                ], 422);
            }

            if ($request->booking_status === 'cancelled' && !$request->payment_status) {
                $updateData['payment_status'] = 'cancelled';
            }

            if ($request->booking_status === 'checked_in') {
                $updateData['actual_check_in_at'] = $booking->actual_check_in_at ?? now();
            }

            $booking->update($updateData);

            // Update room status if checked_in
            if ($request->booking_status == 'checked_in') {
                foreach ($booking->bookingDetails as $detail) {
                    $detail->room->update(['status' => 'occupied']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pemesanan berhasil diupdate',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Verify payment
     */
    public function verifyPayment(Request $request, $bookingId)
    {
        try {
            if ($request->user()?->role !== 'receptionist') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya resepsionis yang dapat memverifikasi pembayaran booking guest.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:verified,rejected',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->payment_method !== 'qris') {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi pembayaran manual hanya berlaku untuk metode QRIS.'
                ], 422);
            }

            if (!in_array((string) $booking->payment_status, ['paid', 'verified'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking ini belum mengirim bukti pembayaran QRIS untuk diverifikasi.'
                ], 422);
            }

            $payment = Payment::where('booking_id', $bookingId)
                ->where('payment_type', 'booking')
                ->latest('id')
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            if ($request->status === 'verified') {
                $requiredAmount = $this->normalizeMoneyAmount($booking->total_amount);
                $submittedAmount = $this->normalizeMoneyAmount($payment->amount);

                if (abs($submittedAmount - $requiredAmount) > 0.00001) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nominal pembayaran tidak sesuai total tagihan. Tidak dapat menyetujui pembayaran.'
                    ], 422);
                }
            }

            $isVerified = $request->status === 'verified';
            $payment->update([
                'status' => $request->status,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'notes' => $request->notes
            ]);

            if ($request->status == 'verified') {
                $booking->update(['payment_status' => 'verified']);
                $this->autoCheckInBookingIfEligible($booking->fresh(['bookingDetails']));
            } else {
                $booking->update(['payment_status' => 'rejected']);
            }

            return response()->json([
                'success' => true,
                'message' => $request->status == 'verified' ? 
                    'Pembayaran berhasil diverifikasi' : 
                    'Pembayaran ditolak',
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Download daily guest report
     */
    public function downloadDailyReport(Request $request)
    {
        try {
            $date = $request->date ?? now()->format('Y-m-d');

            $bookings = Booking::with(['guest', 'bookingDetails.room'])
                ->where(function ($query) use ($date) {
                    $query->whereDate('check_in_date', $date)
                        ->orWhereDate('check_out_date', $date)
                        ->orWhereDate('actual_check_out_at', $date);
                });

            if ($request->user()?->role === 'receptionist') {
                $this->applyReceptionistVisibilityScope($bookings, (int) $request->user()->id);
            }

            $bookings = $bookings
                ->get();

            $checkIns = $bookings->filter(function($booking) use ($date) {
                return $booking->check_in_date->format('Y-m-d') == $date;
            });

            $checkOuts = $bookings->filter(function($booking) use ($date) {
                if ($booking->booking_status === 'checked_out' && $booking->actual_check_out_at) {
                    return $booking->actual_check_out_at->format('Y-m-d') == $date;
                }

                return $booking->check_out_date->format('Y-m-d') == $date;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'total_bookings' => $bookings->count(),
                    'check_ins' => $checkIns->values(),
                    'check_outs' => $checkOuts->values()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Download revenue report
     */
    public function downloadRevenueReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'payment_verified_by' => 'nullable|integer|exists:users,id',
                'checkin_approved_by' => 'nullable|integer|exists:users,id',
                'checkout_processed_by' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Booking::query()
                ->with([
                    'guest:id,full_name,email,username',
                    'checkInApprover:id,full_name,username',
                    'checkOutProcessor:id,full_name,username',
                    'payments' => function ($paymentQuery) {
                        $paymentQuery
                            ->where('payment_type', 'booking')
                            ->orderByDesc('id');
                    },
                    'payments.verifier:id,full_name,username',
                ])
                ->where(function ($dateQuery) use ($request) {
                    $dateQuery
                        ->whereBetween('check_in_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('check_out_date', [$request->start_date, $request->end_date])
                        ->orWhere(function ($overlapQuery) use ($request) {
                            $overlapQuery
                                ->where('check_in_date', '<=', $request->start_date)
                                ->where('check_out_date', '>=', $request->end_date);
                        });
                });

            $paymentVerifiedBy = $request->filled('payment_verified_by')
                ? (int) $request->input('payment_verified_by')
                : null;
            $checkinApprovedBy = $request->filled('checkin_approved_by')
                ? (int) $request->input('checkin_approved_by')
                : null;
            $checkoutProcessedBy = $request->filled('checkout_processed_by')
                ? (int) $request->input('checkout_processed_by')
                : null;

            if ($paymentVerifiedBy || $checkinApprovedBy || $checkoutProcessedBy) {
                $query->where(function ($staffScope) use ($paymentVerifiedBy, $checkinApprovedBy, $checkoutProcessedBy) {
                    $staffScope->whereRaw('1 = 0');

                    if ($paymentVerifiedBy) {
                        $staffScope->orWhereHas('payments', function ($paymentQuery) use ($paymentVerifiedBy) {
                            $paymentQuery
                                ->where('payment_type', 'booking')
                                ->where('status', 'verified')
                                ->where('verified_by', $paymentVerifiedBy);
                        });
                    }

                    if ($checkinApprovedBy) {
                        $staffScope->orWhere('checkin_approved_by', $checkinApprovedBy);
                    }

                    if ($checkoutProcessedBy) {
                        $staffScope->orWhere('checkout_processed_by', $checkoutProcessedBy);
                    }
                });
            }

            $bookings = $query
                ->orderBy('check_in_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalRevenue = $bookings->where('payment_status', 'verified')->sum('total_amount');
            $totalBookings = $bookings->count();
            $verifiedBookings = $bookings->where('payment_status', 'verified')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'total_revenue' => $totalRevenue,
                    'total_bookings' => $totalBookings,
                    'verified_bookings' => $verifiedBookings,
                    'bookings' => $bookings,
                    'filters' => [
                        'payment_verified_by' => $request->input('payment_verified_by'),
                        'checkin_approved_by' => $request->input('checkin_approved_by'),
                        'checkout_processed_by' => $request->input('checkout_processed_by'),
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pendapatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::query(),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            if ($booking->guest_id != $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($booking->booking_status != 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking cannot be cancelled at this stage'
                ], 400);
            }

            if (in_array($booking->payment_status, ['paid', 'verified'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak dapat dibatalkan karena pembayaran sudah dilakukan.'
                ], 400);
            }

            DB::transaction(function () use ($booking) {
                $booking->update([
                    'booking_status' => 'cancelled',
                    'payment_status' => 'cancelled',
                ]);

                Payment::where('booking_id', $booking->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'notes' => 'Dibatalkan otomatis karena booking dibatalkan oleh tamu.',
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Process check-out (self check-out)
     */
    public function guestCheckOut(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::with(['bookingDetails.room']),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ((int) $booking->guest_id !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan check-out pada pemesanan ini.'
                ], 403);
            }

            if ($booking->booking_status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-out hanya bisa dilakukan saat status booking checked_in. Status saat ini: ' . $booking->booking_status
                ], 400);
            }

            $actualCheckOutAt = $request->filled('actual_check_out_at')
                ? Carbon::parse($request->input('actual_check_out_at'), self::HOTEL_TIMEZONE)
                : now(self::HOTEL_TIMEZONE);

            $scheduledCheckOutAt = $this->getScheduledCheckOutAt($booking);
            $lateCheckoutSnapshot = $this->buildLateCheckoutSnapshot($booking, $actualCheckOutAt);
            $lateCheckoutHours = (int) ($lateCheckoutSnapshot['late_checkout_hours'] ?? 0);
            $lateCheckoutPenalty = (float) ($lateCheckoutSnapshot['late_checkout_penalty'] ?? 0);

            if ($lateCheckoutPenalty <= 0) {
                $this->finalizeCheckout($booking, $actualCheckOutAt, $lateCheckoutHours, $lateCheckoutPenalty);

                return response()->json([
                    'success' => true,
                    'message' => 'Check-out berhasil diproses.',
                    'data' => array_merge(
                        $booking->fresh(['bookingDetails.room.roomType', 'guest'])->toArray(),
                        [
                            'scheduled_check_out_at' => $scheduledCheckOutAt->toDateTimeString(),
                            'actual_check_out_at' => $actualCheckOutAt->toDateTimeString(),
                            'grace_minutes' => self::LATE_CHECKOUT_GRACE_MINUTES,
                            'late_checkout_hours' => $lateCheckoutHours,
                            'late_checkout_penalty' => $lateCheckoutPenalty,
                            'late_checkout_penalty_per_hour' => self::LATE_CHECKOUT_PENALTY_PER_HOUR,
                            'requires_penalty_payment' => false,
                        ]
                    )
                ]);
            }

            $pendingCheckOutAt = $booking->pending_check_out_at
                ? $booking->pending_check_out_at->copy()->setTimezone(self::HOTEL_TIMEZONE)
                : $actualCheckOutAt;
            if ($actualCheckOutAt->greaterThan($pendingCheckOutAt)) {
                $pendingCheckOutAt = $actualCheckOutAt->copy();
            }
            $pendingLateHours = max(
                (int) $booking->pending_late_checkout_hours,
                $lateCheckoutHours
            );
            $pendingLatePenalty = $pendingLateHours * self::LATE_CHECKOUT_PENALTY_PER_HOUR;

            $booking->update([
                'pending_check_out_at' => $pendingCheckOutAt->copy()->utc(),
                'pending_late_checkout_hours' => $pendingLateHours,
                'pending_late_checkout_penalty' => $pendingLatePenalty,
            ]);

            $latestPenaltyPayment = Payment::where('booking_id', $booking->id)
                ->where('payment_type', 'late_checkout_penalty')
                ->latest('id')
                ->first();

            if ($latestPenaltyPayment && $latestPenaltyPayment->status === 'verified') {
                $this->finalizeCheckout(
                    $booking,
                    $booking->pending_check_out_at ?: $pendingCheckOutAt,
                    (int) $booking->pending_late_checkout_hours,
                    (float) $booking->pending_late_checkout_penalty
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Check-out berhasil diproses setelah denda keterlambatan terverifikasi.',
                    'data' => array_merge(
                        $booking->fresh(['bookingDetails.room.roomType', 'guest'])->toArray(),
                        [
                            'requires_penalty_payment' => false,
                            'late_checkout_hours' => (int) $booking->late_checkout_hours,
                            'late_checkout_penalty' => (float) $booking->late_checkout_penalty,
                            'late_checkout_penalty_per_hour' => self::LATE_CHECKOUT_PENALTY_PER_HOUR,
                            'grace_minutes' => self::LATE_CHECKOUT_GRACE_MINUTES,
                        ]
                    )
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Check-out belum bisa diselesaikan. Silakan bayar denda keterlambatan terlebih dahulu.',
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'scheduled_check_out_at' => $scheduledCheckOutAt->toDateTimeString(),
                    'actual_check_out_at' => $pendingCheckOutAt->toDateTimeString(),
                    'grace_minutes' => self::LATE_CHECKOUT_GRACE_MINUTES,
                    'late_checkout_hours' => $pendingLateHours,
                    'late_checkout_penalty' => $pendingLatePenalty,
                    'late_checkout_penalty_per_hour' => self::LATE_CHECKOUT_PENALTY_PER_HOUR,
                    'requires_penalty_payment' => true,
                    'penalty_payment_status' => $latestPenaltyPayment?->status,
                    'penalty_payment_method' => $latestPenaltyPayment?->payment_method,
                    'next_step' => [
                        'path' => "/guest/penalty-payment/{$booking->id}",
                        'type' => 'pay_late_checkout_penalty',
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses check-out guest: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Get late checkout penalty detail for payment form.
     */
    public function getLateCheckoutPenaltyDetail(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::with(['guest', 'bookingDetails.room.roomType']),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ((int) $booking->guest_id !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pemesanan ini.'
                ], 403);
            }

            if ($booking->booking_status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Form denda checkout hanya tersedia saat status booking checked_in.'
                ], 422);
            }

            $lateCheckoutSnapshot = $this->buildLateCheckoutSnapshot($booking);
            $effectiveLatePenalty = (float) ($lateCheckoutSnapshot['late_checkout_penalty'] ?? 0);
            $effectiveLateHours = (int) ($lateCheckoutSnapshot['late_checkout_hours'] ?? 0);
            $effectivePendingCheckOutAt = isset($lateCheckoutSnapshot['actual_check_out_at'])
                ? Carbon::parse($lateCheckoutSnapshot['actual_check_out_at'], self::HOTEL_TIMEZONE)
                : null;

            if ($effectiveLatePenalty <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada tagihan denda checkout untuk booking ini.'
                ], 422);
            }

            if ($effectivePendingCheckOutAt) {
                $booking->update([
                    'pending_check_out_at' => $effectivePendingCheckOutAt->copy()->utc(),
                    'pending_late_checkout_hours' => $effectiveLateHours,
                    'pending_late_checkout_penalty' => $effectiveLatePenalty,
                ]);
                $booking = $booking->fresh(['guest', 'bookingDetails.room.roomType']);
            }

            $latestPenaltyPayment = Payment::where('booking_id', $booking->id)
                ->where('payment_type', 'late_checkout_penalty')
                ->latest('id')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking,
                    'late_checkout_hours' => $effectiveLateHours,
                    'late_checkout_penalty' => $effectiveLatePenalty,
                    'late_checkout_penalty_per_hour' => self::LATE_CHECKOUT_PENALTY_PER_HOUR,
                    'pending_check_out_at' => $effectivePendingCheckOutAt?->toDateTimeString(),
                    'scheduled_check_out_at' => $lateCheckoutSnapshot['scheduled_check_out_at'] ?? null,
                    'grace_minutes' => self::LATE_CHECKOUT_GRACE_MINUTES,
                    'payment_status' => $latestPenaltyPayment?->status,
                    'payment_method' => $latestPenaltyPayment?->payment_method,
                    'payment_proof' => $latestPenaltyPayment?->proof_image,
                    'requires_penalty_payment' => true,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest: Submit late checkout penalty payment.
     */
    public function submitLateCheckoutPenaltyPayment(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::with(['bookingDetails.room']),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ((int) $booking->guest_id !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pemesanan ini.'
                ], 403);
            }

            if ($booking->booking_status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran denda checkout hanya untuk booking berstatus checked_in.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'payment_method' => 'required|in:qris,cash',
                'paid_amount' => 'required|numeric|min:0',
                'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ((float) $booking->pending_late_checkout_penalty <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada tagihan denda checkout untuk booking ini.'
                ], 422);
            }

            $requiredPenalty = $this->normalizeMoneyAmount($booking->pending_late_checkout_penalty);
            $paidAmount = $this->normalizeMoneyAmount($request->input('paid_amount'));

            if ($paidAmount + 0.00001 < $requiredPenalty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal bayar denda tidak boleh kurang dari total denda keterlambatan.',
                    'errors' => [
                        'paid_amount' => [
                            'Nominal bayar harus minimal sama dengan total denda.'
                        ]
                    ]
                ], 422);
            }

            $paymentMethod = strtolower((string) $request->input('payment_method'));
            $proofPath = null;
            if ($paymentMethod === 'qris') {
                if (!$request->hasFile('payment_proof')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bukti pembayaran QRIS wajib diupload untuk denda checkout.'
                    ], 422);
                }

                $proofPath = $request->file('payment_proof')->store('penalty-payment-proofs', 'public');
            }

            $latestPenaltyPayment = Payment::where('booking_id', $booking->id)
                ->where('payment_type', 'late_checkout_penalty')
                ->latest('id')
                ->first();

            if ($paymentMethod === 'qris' && $latestPenaltyPayment && $latestPenaltyPayment->status === 'pending') {
                // Re-upload while still pending: update the latest pending proof.
                $penaltyPayment = $latestPenaltyPayment;
            } else {
                // Re-upload after rejected/verified (or first-time payment): create a fresh record
                // so verification queue and history remain consistent.
                $penaltyPayment = new Payment();
                $penaltyPayment->booking_id = $booking->id;
                $penaltyPayment->payment_type = 'late_checkout_penalty';
            }

            $penaltyPayment->amount = $requiredPenalty;
            $penaltyPayment->payment_method = $paymentMethod;
            $penaltyPayment->payment_date = now();
            $penaltyPayment->proof_image = $paymentMethod === 'cash'
                ? null
                : ($proofPath ?: $penaltyPayment->proof_image);

            if ($paymentMethod === 'cash') {
                $penaltyPayment->status = 'verified';
                $penaltyPayment->verified_at = now();
                $penaltyPayment->verified_by = null;
                $penaltyPayment->notes = 'Pembayaran denda checkout tunai diverifikasi otomatis.';
                $penaltyPayment->save();
                $this->closeOtherPendingLateCheckoutPenaltyPayments($booking->id, (int) $penaltyPayment->id);

                $this->finalizeCheckout(
                    $booking,
                    $booking->pending_check_out_at ?: now(self::HOTEL_TIMEZONE),
                    (int) $booking->pending_late_checkout_hours,
                    (float) $booking->pending_late_checkout_penalty
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran denda tunai berhasil. Check-out selesai.',
                    'data' => array_merge(
                        $booking->fresh(['bookingDetails.room.roomType', 'guest', 'payments'])->toArray(),
                        [
                            'late_checkout_hours' => (int) $booking->late_checkout_hours,
                            'late_checkout_penalty' => (float) $booking->late_checkout_penalty,
                            'requires_penalty_payment' => false,
                        ]
                    )
                ]);
            }

            $penaltyPayment->status = 'verified';
            $penaltyPayment->verified_at = now();
            $penaltyPayment->verified_by = null;
            $penaltyPayment->notes = 'Pembayaran denda checkout QRIS diverifikasi otomatis.';
            $penaltyPayment->save();
            $this->closeOtherPendingLateCheckoutPenaltyPayments($booking->id, (int) $penaltyPayment->id);

            $this->finalizeCheckout(
                $booking,
                $booking->pending_check_out_at ?: now(self::HOTEL_TIMEZONE),
                (int) $booking->pending_late_checkout_hours,
                (float) $booking->pending_late_checkout_penalty
            );

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran denda QRIS berhasil. Check-out selesai.',
                'data' => array_merge(
                    $booking->fresh(['bookingDetails.room.roomType', 'guest', 'payments'])->toArray(),
                    [
                        'late_checkout_hours' => (int) $booking->late_checkout_hours,
                        'late_checkout_penalty' => (float) $booking->late_checkout_penalty,
                        'requires_receptionist_verification' => false,
                        'requires_penalty_payment' => false,
                    ]
                )
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Public: Print booking without authentication (for scanned barcode flow).
     */
    public function printBookingPublic(Request $request, $bookingId)
    {
        try {
            $booking = $this->findBookingByIdentifier(
                Booking::with(['guest', 'bookingDetails.room.roomType', 'payments', 'barcode']),
                $bookingId
            )->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->payment_status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Struk hanya dapat dicetak setelah pembayaran terverifikasi (Lunas/Bayar di Tempat).'
                ], 422);
            }

            $this->createOrRefreshBookingBarcode($booking);
            $booking->load('barcode');

            $roomType = $booking->bookingDetails->first()?->room?->roomType;
            $bookingData = $booking->toArray();
            $bookingData['room_type'] = $roomType;
            $bookingData['barcode'] = $booking->barcode;

            return response()->json([
                'success' => true,
                'data' => $bookingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create/update barcode metadata for a booking.
     */
    private function createOrRefreshBookingBarcode(Booking $booking): BookingBarcode
    {
        $payloadPath = '/p/' . rawurlencode((string) $booking->id);
        $barcodeNumber = 'HBG' . str_pad((string) $booking->id, 10, '0', STR_PAD_LEFT);

        return BookingBarcode::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'barcode_number' => $barcodeNumber,
                'barcode_type' => 'CODE128',
                'barcode_data' => $payloadPath,
                'generated_at' => now(),
            ]
        );
    }

    /**
     * Resolve booking by numeric ID or booking number.
     */
    private function findBookingByIdentifier($query, $bookingIdentifier)
    {
        return $query->where(function ($innerQuery) use ($bookingIdentifier) {
            $innerQuery->where('id', $bookingIdentifier)
                ->orWhere('booking_number', $bookingIdentifier);
        });
    }

    /**
     * Restrict receptionist data to tasks handled by current receptionist
     * while keeping pending queues visible for operational flow.
     */
    private function applyReceptionistVisibilityScope($query, int $receptionistId): void
    {
        $query->where(function ($scope) use ($receptionistId) {
            $scope->where(function ($pendingPaymentScope) {
                $pendingPaymentScope
                    ->where('payment_method', 'qris')
                    ->where('booking_status', 'confirmed')
                    ->where('payment_status', 'paid');
            })
            ->orWhere(function ($pendingCheckinScope) {
                $pendingCheckinScope
                    ->where('booking_status', 'confirmed')
                    ->where('payment_status', 'verified')
                    ->whereNotNull('checkin_receipt_proof');
            })
            ->orWhere('checkin_approved_by', $receptionistId)
            ->orWhere('checkout_processed_by', $receptionistId)
            ->orWhereHas('payments', function ($paymentScope) use ($receptionistId) {
                $paymentScope
                    ->where('payment_type', 'booking')
                    ->where('verified_by', $receptionistId);
            })
            ->orWhereHas('payments', function ($paymentScope) use ($receptionistId) {
                $paymentScope
                    ->where('payment_type', 'late_checkout_penalty')
                    ->where('verified_by', $receptionistId);
            });
        });
    }

    /**
     * Auto check-in is disabled: all check-ins require receptionist approval.
     */
    private function autoCheckInBookingIfEligible(Booking $booking): bool
    {
        return false;
    }

    /**
     * Ensure a single booking reflects automatic check-in rule in real time.
     */
    private function refreshAutoCheckInForBooking(Booking $booking, array $relations = []): Booking
    {
        $booking->loadMissing('bookingDetails');
        $booking = $this->ensureActualCheckInTimestamp($booking);

        if ($this->autoCheckInBookingIfEligible($booking)) {
            $freshBooking = $booking->fresh($relations);
            return $freshBooking ?? $booking;
        }

        return $booking;
    }

    /**
     * Ensure a booking collection reflects automatic check-in rule in real time.
     */
    private function refreshAutoCheckInForCollection($bookings, array $relations = [])
    {
        return $bookings->map(function ($booking) use ($relations) {
            return $this->refreshAutoCheckInForBooking($booking, $relations);
        });
    }

    /**
     * Backfill actual check-in timestamp for legacy records that were checked-in before this field existed.
     */
    private function ensureActualCheckInTimestamp(Booking $booking): Booking
    {
        $isCheckedIn = in_array((string) $booking->booking_status, ['checked_in', 'checked_out'], true);
        if (!$isCheckedIn || $booking->actual_check_in_at) {
            return $booking;
        }

        $fallbackCheckInAt = $booking->updated_at ?? now();
        $booking->update(['actual_check_in_at' => $fallbackCheckInAt]);

        return $booking->fresh() ?? $booking;
    }

    /**
     * Build current late checkout snapshot based on scheduled checkout and latest known checkout reference.
     */
    private function buildLateCheckoutSnapshot(Booking $booking, ?Carbon $referenceAt = null): array
    {
        $scheduledCheckOutAt = $this->getScheduledCheckOutAt($booking);

        $nowAtHotelTz = $referenceAt
            ? $referenceAt->copy()->setTimezone(self::HOTEL_TIMEZONE)
            : now(self::HOTEL_TIMEZONE);

        $pendingCheckOutAt = $booking->pending_check_out_at
            ? $booking->pending_check_out_at->copy()->setTimezone(self::HOTEL_TIMEZONE)
            : null;

        $effectiveCheckOutAt = $pendingCheckOutAt
            ? ($nowAtHotelTz->greaterThan($pendingCheckOutAt) ? $nowAtHotelTz : $pendingCheckOutAt)
            : $nowAtHotelTz;

        $lateMinutes = $scheduledCheckOutAt->diffInMinutes($effectiveCheckOutAt, false);
        $billableLateMinutes = max(0, $lateMinutes - self::LATE_CHECKOUT_GRACE_MINUTES);
        $computedHours = $billableLateMinutes > 0 ? (int) ceil($billableLateMinutes / 60) : 0;

        $storedHours = max(0, (int) $booking->pending_late_checkout_hours);
        $effectiveHours = max($storedHours, $computedHours);
        $effectivePenalty = $effectiveHours * self::LATE_CHECKOUT_PENALTY_PER_HOUR;

        $latestPenaltyPayment = Payment::where('booking_id', $booking->id)
            ->where('payment_type', 'late_checkout_penalty')
            ->latest('id')
            ->first();

        return [
            'scheduled_check_out_at' => $scheduledCheckOutAt->toDateTimeString(),
            'actual_check_out_at' => $effectiveCheckOutAt->toDateTimeString(),
            'late_checkout_hours' => $effectiveHours,
            'late_checkout_penalty' => (float) $effectivePenalty,
            'late_checkout_penalty_per_hour' => self::LATE_CHECKOUT_PENALTY_PER_HOUR,
            'grace_minutes' => self::LATE_CHECKOUT_GRACE_MINUTES,
            'requires_penalty_payment' => $effectivePenalty > 0 && $latestPenaltyPayment?->status !== 'verified',
            'penalty_payment_status' => $latestPenaltyPayment?->status,
            'penalty_payment_method' => $latestPenaltyPayment?->payment_method,
        ];
    }

    private function getScheduledCheckOutAt(Booking $booking): Carbon
    {
        return Carbon::parse(
            $booking->check_out_date->format('Y-m-d'),
            self::HOTEL_TIMEZONE
        )->setTime(self::STANDARD_CHECKOUT_HOUR, 0, 0);
    }

    /**
     * Close stale pending penalty payment records for the same booking
     * once one payment has been auto-verified.
     */
    private function closeOtherPendingLateCheckoutPenaltyPayments(int $bookingId, int $verifiedPaymentId): void
    {
        Payment::query()
            ->where('booking_id', $bookingId)
            ->where('payment_type', 'late_checkout_penalty')
            ->where('status', 'pending')
            ->where('id', '!=', $verifiedPaymentId)
            ->update([
                'status' => 'rejected',
                'verified_at' => now(),
                'verified_by' => null,
                'notes' => 'Ditutup otomatis karena booking sudah checkout dengan pembayaran denda lain yang valid.',
            ]);
    }

    /**
     * Finalize booking checkout and release occupied rooms.
     */
    private function finalizeCheckout(
        Booking $booking,
        Carbon $actualCheckOutAt,
        int $lateCheckoutHours,
        float $lateCheckoutPenalty
    ): void {
        DB::transaction(function () use ($booking, $actualCheckOutAt, $lateCheckoutHours, $lateCheckoutPenalty) {
            $booking->update([
                'booking_status' => 'checked_out',
                'actual_check_out_at' => $actualCheckOutAt->copy()->utc(),
                'late_checkout_hours' => $lateCheckoutHours,
                'late_checkout_penalty' => $lateCheckoutPenalty,
                'pending_check_out_at' => null,
                'pending_late_checkout_hours' => 0,
                'pending_late_checkout_penalty' => 0,
            ]);

            $booking->loadMissing('bookingDetails.room');
            foreach ($booking->bookingDetails as $detail) {
                Room::where('id', $detail->room_id)->update(['status' => 'available']);
            }
        });
    }

    /**
     * Normalize currency-like input into decimal number.
     */
    private function normalizeMoneyAmount(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return 0.0;
        }

        $text = preg_replace('/[^\d,.\-]/', '', $text) ?? '';

        $lastComma = strrpos($text, ',');
        $lastDot = strrpos($text, '.');

        if ($lastComma !== false && $lastDot !== false) {
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
            $thousandsSeparator = $decimalSeparator === ',' ? '.' : ',';
            $text = str_replace($thousandsSeparator, '', $text);
            $text = str_replace($decimalSeparator, '.', $text);
        } elseif ($lastComma !== false) {
            $text = str_replace(',', '.', $text);
        }

        return is_numeric($text) ? (float) $text : 0.0;
    }

    /**
     * Helper: Check room availability
     */
    private function checkRoomAvailability($roomTypeId, $checkIn, $checkOut)
    {
        $bookedRooms = BookingDetail::whereIn('booking_id', function ($query) use ($checkIn, $checkOut) {
            $query->select('id')
                ->from('bookings')
                // Occupancy uses [check_in_date, check_out_date) semantics:
                // a booking conflicts only when it starts before requested checkout
                // and ends after requested check-in.
                ->where('check_in_date', '<', $checkOut)
                ->where('check_out_date', '>', $checkIn)
                ->where('booking_status', '!=', 'cancelled')
                ->where('booking_status', '!=', 'checked_out');
        })->pluck('room_id');

        return Room::where('room_type_id', $roomTypeId)
            ->where('is_active', true)
            ->where('status', 'available')
            ->whereNotIn('id', $bookedRooms)
            ->get();
    }
}
