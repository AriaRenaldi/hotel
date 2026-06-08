<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReseptionistController extends Controller
{
    private const HOTEL_TIMEZONE = 'Asia/Jakarta';

    /**
     * Receptionist: Dashboard with overview data
     */
    public function dashboard(Request $request)
    {
        try {
            $this->syncCashAutoCheckInStatuses();

            $today = now()->format('Y-m-d');
            $availableRooms = $this->getRealtimeAvailableRoomsCount($today);

            $data = [
                'today_checkins' => Booking::whereDate('check_in_date', $today)->count(),
                'today_checkouts' => Booking::where('booking_status', 'checked_out')
                    ->whereDate('actual_check_out_at', $today)
                    ->count(),
                'pending_payments' => Booking::whereIn('payment_status', ['pending', 'paid'])->count(),
                'available_rooms' => $availableRooms,
                'occupied_rooms' => Room::where('status', 'occupied')->count(),
                'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
                'total_bookings_today' => Booking::whereDate('created_at', $today)->count(),
                'checked_in_today' => Booking::whereDate('check_in_date', $today)
                    ->where('booking_status', 'checked_in')->count(),
                'pending_checkins' => Booking::whereDate('check_in_date', $today)
                    ->where('booking_status', 'confirmed')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get detailed stats
     */
    public function getStats(Request $request)
    {
        try {
            $this->syncCashAutoCheckInStatuses();

            $today = now()->format('Y-m-d');
            $thisWeek = now()->startOfWeek()->format('Y-m-d');
            $thisMonth = now()->startOfMonth()->format('Y-m-d');
            $availableRooms = $this->getRealtimeAvailableRoomsCount($today);

            $data = [
                'today' => [
                    'checkins' => Booking::whereDate('check_in_date', $today)->count(),
                    'checkouts' => Booking::where('booking_status', 'checked_out')
                        ->whereDate('actual_check_out_at', $today)
                        ->count(),
                    'new_bookings' => Booking::whereDate('created_at', $today)->count(),
                ],
                'this_week' => [
                    'checkins' => Booking::where('check_in_date', '>=', $thisWeek)->count(),
                    'bookings' => Booking::where('created_at', '>=', $thisWeek)->count(),
                ],
                'this_month' => [
                    'checkins' => Booking::where('check_in_date', '>=', $thisMonth)->count(),
                    'bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
                    'revenue' => Booking::where('payment_status', 'verified')
                        ->where('created_at', '>=', $thisMonth)->sum('total_amount'),
                ],
                'pending_payments' => Booking::whereIn('payment_status', ['pending', 'paid'])->count(),
                'available_rooms' => $availableRooms,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Process check-in
     */
    public function processCheckIn(Request $request, $bookingId)
    {
        try {
            $booking = Booking::with(['bookingDetails.room'])->find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak ditemukan'
                ], 404);
            }

            if ($booking->booking_status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan tidak dalam status confirmed. Status saat ini: ' . $booking->booking_status
                ], 400);
            }

            if ($booking->payment_status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in tidak bisa diproses karena pembayaran belum terverifikasi.'
                ], 422);
            }

            if (!$booking->checkin_receipt_proof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in belum bisa disetujui. Tamu harus upload struk booking terlebih dahulu.'
                ], 422);
            }

            $hotelNow = now(self::HOTEL_TIMEZONE);
            $bookingCheckInDate = $booking->check_in_date->copy()->timezone(self::HOTEL_TIMEZONE)->toDateString();
            $today = $hotelNow->toDateString();

            if ($bookingCheckInDate > $today) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in belum dapat diproses karena belum masuk tanggal check-in.'
                ], 422);
            }

            if ($bookingCheckInDate === $today && $booking->requested_checkin_time) {
                $requestedDateTime = Carbon::parse(
                    $bookingCheckInDate . ' ' . substr((string) $booking->requested_checkin_time, 0, 8),
                    self::HOTEL_TIMEZONE
                );

                if ($hotelNow->lt($requestedDateTime)) {
                    $requestedLabel = $requestedDateTime->format('H:i') . ' WIB';
                    return response()->json([
                        'success' => false,
                        'message' => "Check-in baru dapat disetujui setelah jam {$requestedLabel} sesuai pilihan tamu."
                    ], 422);
                }
            }

            DB::beginTransaction();

            $booking->update([
                'booking_status' => 'checked_in',
                'actual_check_in_at' => $booking->actual_check_in_at ?? now(),
                'checkin_approved_by' => $request->user()->id,
                'checkin_approved_at' => now(),
            ]);

            // Update room status to occupied
            foreach ($booking->bookingDetails as $detail) {
                Room::where('id', $detail->room_id)->update(['status' => 'occupied']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil diproses',
                'data' => $booking->fresh(['bookingDetails.room.roomType', 'guest'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get check-ins report for a specific date
     */
    public function getCheckInsReport(Request $request)
    {
        try {
            $this->syncCashAutoCheckInStatuses();

            $date = $request->input('date', now()->format('Y-m-d'));
            $receptionistId = (int) $request->user()->id;

            $bookings = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments'])
                ->whereDate('check_in_date', $date)
                ->where('checkin_approved_by', $receptionistId)
                ->orderBy('created_at', 'desc')
                ->get();

            $summary = [
                'date' => $date,
                'total_checkins' => $bookings->count(),
                'confirmed' => $bookings->where('booking_status', 'confirmed')->count(),
                'checked_in' => $bookings->where('booking_status', 'checked_in')->count(),
                'cancelled' => $bookings->where('booking_status', 'cancelled')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'bookings' => $bookings,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get check-outs report for a specific date
     */
    public function getCheckOutsReport(Request $request)
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $receptionistId = (int) $request->user()->id;

            $bookings = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments'])
                ->where('booking_status', 'checked_out')
                ->whereDate('actual_check_out_at', $date)
                ->where(function ($scope) use ($receptionistId) {
                    $scope
                        ->where('checkout_processed_by', $receptionistId)
                        ->orWhere(function ($guestCheckoutScope) use ($receptionistId) {
                            $guestCheckoutScope
                                ->whereNull('checkout_processed_by')
                                ->where('checkin_approved_by', $receptionistId);
                        });
                })
                ->orderBy('actual_check_out_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $summary = [
                'date' => $date,
                'total_checkouts' => $bookings->count(),
                'checked_in' => $bookings->where('booking_status', 'checked_in')->count(),
                'checked_out' => $bookings->where('booking_status', 'checked_out')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'bookings' => $bookings,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan check-out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Export daily report data (for Excel generation on frontend)
     */
    public function exportReportExcel(Request $request)
    {
        try {
            $this->syncCashAutoCheckInStatuses();

            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'type' => 'required|in:checkins,checkouts,both',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $date = $request->date;
            $type = $request->type;
            $receptionistId = (int) $request->user()->id;

            $query = Booking::with(['guest', 'bookingDetails.room.roomType']);

            if ($type === 'checkins') {
                $query
                    ->whereDate('check_in_date', $date)
                    ->where('checkin_approved_by', $receptionistId);
            } elseif ($type === 'checkouts') {
                $query
                    ->whereDate('actual_check_out_at', $date)
                    ->where(function ($scope) use ($receptionistId) {
                        $scope
                            ->where('checkout_processed_by', $receptionistId)
                            ->orWhere(function ($guestCheckoutScope) use ($receptionistId) {
                                $guestCheckoutScope
                                    ->whereNull('checkout_processed_by')
                                    ->where('checkin_approved_by', $receptionistId);
                            });
                    });
            } else {
                $query->where(function ($q) use ($date, $receptionistId) {
                    $q->where(function ($checkInQuery) use ($date, $receptionistId) {
                        $checkInQuery
                            ->whereDate('check_in_date', $date)
                            ->where('checkin_approved_by', $receptionistId);
                    })->orWhere(function ($checkOutQuery) use ($date, $receptionistId) {
                        $checkOutQuery
                            ->whereDate('actual_check_out_at', $date)
                            ->where(function ($scope) use ($receptionistId) {
                                $scope
                                    ->where('checkout_processed_by', $receptionistId)
                                    ->orWhere(function ($guestCheckoutScope) use ($receptionistId) {
                                        $guestCheckoutScope
                                            ->whereNull('checkout_processed_by')
                                            ->where('checkin_approved_by', $receptionistId);
                                    });
                            });
                    });
                });
            }

            $bookings = $query->orderBy('created_at', 'asc')->get();

            $records = $bookings->map(function ($booking) use ($date, $receptionistId) {
                $isCheckIn = $booking->check_in_date->format('Y-m-d') === $date
                    && (int) $booking->checkin_approved_by === $receptionistId;
                $belongsToReceptionistCheckOut = (
                    (int) $booking->checkout_processed_by === $receptionistId
                ) || (
                    $booking->checkout_processed_by === null
                    && (int) $booking->checkin_approved_by === $receptionistId
                );
                $isCheckOut = $booking->actual_check_out_at
                    && $booking->actual_check_out_at->format('Y-m-d') === $date
                    && $belongsToReceptionistCheckOut;
                $activityType = $isCheckIn && $isCheckOut
                    ? 'Check-In & Check-Out'
                    : ($isCheckOut ? 'Check-Out' : 'Check-In');

                return [
                    'booking_number' => $booking->booking_number,
                    'guest_name' => $booking->guest->full_name ?? '-',
                    'guest_email' => $booking->guest->email ?? '-',
                    'guest_phone' => $booking->guest->phone ?? '-',
                    'check_in_date' => $booking->check_in_date->format('Y-m-d'),
                    'check_out_date' => $booking->check_out_date->format('Y-m-d'),
                    'total_rooms' => $booking->total_rooms,
                    'total_nights' => $booking->total_nights,
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'type' => $activityType,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'report_date' => $date,
                    'report_type' => $type,
                    'total_records' => $records->count(),
                    'records' => $records,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get late checkout penalty payments for verification queue.
     */
    public function getLateCheckoutPenaltyVerificationQueue(Request $request)
    {
        try {
            $this->autoVerifyPendingQrisLateCheckoutPenaltyPayments();

            $status = $request->input('status', 'pending');
            $page = max(1, (int) $request->input('page', 1));
            $perPage = max(1, min(100, (int) $request->input('per_page', 6)));
            $allowedStatuses = ['pending', 'verified', 'rejected'];
            if (!in_array($status, $allowedStatuses, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid.'
                ], 422);
            }

            $query = Payment::with([
                    'booking.guest',
                    'booking.bookingDetails.room.roomType',
                    'verifier',
                ])
                ->where('payment_type', 'late_checkout_penalty')
                ->where('status', $status);

            $payments = $query
                ->orderByRaw('CASE WHEN status = "pending" THEN 0 ELSE 1 END')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status,
                    'total' => $payments->total(),
                    'payments' => $payments->items(),
                    'pagination' => [
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                        'per_page' => $payments->perPage(),
                        'total' => $payments->total(),
                        'from' => $payments->firstItem(),
                        'to' => $payments->lastItem(),
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil antrean verifikasi denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Verify/reject late checkout penalty payment.
     */
    public function verifyLateCheckoutPenaltyPayment(Request $request, $paymentId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:verified,rejected',
                'notes' => 'nullable|string|required_if:status,rejected|min:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $payment = Payment::with(['booking.bookingDetails.room'])
                ->where('payment_type', 'late_checkout_penalty')
                ->find($paymentId);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembayaran denda tidak ditemukan'
                ], 404);
            }

            $booking = $payment->booking;
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking untuk pembayaran denda tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            $payment->update([
                'status' => $request->input('status'),
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
                'notes' => $request->input('notes') ?: (
                    $request->input('status') === 'verified'
                        ? 'Pembayaran denda checkout diverifikasi resepsionis.'
                        : 'Pembayaran denda checkout ditolak resepsionis.'
                ),
            ]);

            if ($request->input('status') === 'verified') {
                $resolvedCheckOutAt = $booking->pending_check_out_at
                    ? $booking->pending_check_out_at->copy()
                    : now(self::HOTEL_TIMEZONE);

                $booking->update([
                    'booking_status' => 'checked_out',
                    'actual_check_out_at' => $resolvedCheckOutAt->utc(),
                    'checkout_processed_by' => $request->user()->id,
                    'checkout_processed_at' => now(),
                    'late_checkout_hours' => (int) $booking->pending_late_checkout_hours,
                    'late_checkout_penalty' => (float) $booking->pending_late_checkout_penalty,
                    'pending_check_out_at' => null,
                    'pending_late_checkout_hours' => 0,
                    'pending_late_checkout_penalty' => 0,
                ]);

                foreach ($booking->bookingDetails as $detail) {
                    Room::where('id', $detail->room_id)->update(['status' => 'available']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->input('status') === 'verified'
                    ? 'Pembayaran denda checkout berhasil diverifikasi dan booking berhasil check-out.'
                    : 'Pembayaran denda checkout ditolak. Guest harus upload ulang bukti.',
                'data' => [
                    'payment' => $payment->fresh(['booking.guest', 'verifier']),
                    'booking' => $booking->fresh(['guest', 'bookingDetails.room.roomType']),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi pembayaran denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Receptionist: Get late checkout penalty payment report.
     */
    public function getLateCheckoutPenaltyReport(Request $request)
    {
        try {
            $this->autoVerifyPendingQrisLateCheckoutPenaltyPayments();

            $date = $request->input('date', now(self::HOTEL_TIMEZONE)->toDateString());
            $receptionistId = (int) $request->user()->id;
            $start = Carbon::parse($date, self::HOTEL_TIMEZONE)->startOfDay();
            $end = Carbon::parse($date, self::HOTEL_TIMEZONE)->endOfDay();
            $page = max(1, (int) $request->input('page', 1));
            $perPage = max(1, min(100, (int) $request->input('per_page', 6)));

            $baseQuery = Payment::query()
                ->where('payment_type', 'late_checkout_penalty')
                ->whereIn('status', ['verified', 'rejected'])
                ->whereBetween('verified_at', [$start, $end])
                ->where(function ($scope) use ($receptionistId) {
                    $scope->where('verified_by', $receptionistId)
                        ->orWhere(function ($autoScope) use ($receptionistId) {
                            $autoScope
                                ->whereNull('verified_by')
                                ->where('status', 'verified')
                                ->whereIn('payment_method', ['cash', 'qris'])
                                ->whereHas('booking', function ($bookingScope) use ($receptionistId) {
                                    $bookingScope
                                        ->where('checkin_approved_by', $receptionistId)
                                        ->orWhere('checkout_processed_by', $receptionistId);
                                });
                        });
                });

            $summaryRaw = (clone $baseQuery)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_count,
                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
                    SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_penalty_amount
                ')
                ->first();

            $paymentsPaginator = (clone $baseQuery)
                ->with(['booking.guest', 'booking.bookingDetails.room.roomType', 'verifier'])
                ->orderBy('verified_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $verifiedPenaltyAmount = (float) ($summaryRaw->verified_penalty_amount ?? 0);
            $verifiedCount = (int) ($summaryRaw->verified_count ?? 0);
            $rejectedCount = (int) ($summaryRaw->rejected_count ?? 0);
            $totalTransactions = (int) ($summaryRaw->total_transactions ?? 0);

            $summary = [
                'date' => $date,
                'total_transactions' => $totalTransactions,
                'pending' => 0,
                'verified' => $verifiedCount,
                'rejected' => $rejectedCount,
                'total_penalty_amount' => $verifiedPenaltyAmount,
                'verified_penalty_amount' => $verifiedPenaltyAmount,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'payments' => $paymentsPaginator->items(),
                    'pagination' => [
                        'current_page' => $paymentsPaginator->currentPage(),
                        'last_page' => $paymentsPaginator->lastPage(),
                        'per_page' => $paymentsPaginator->perPage(),
                        'total' => $paymentsPaginator->total(),
                        'from' => $paymentsPaginator->firstItem(),
                        'to' => $paymentsPaginator->lastItem(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pembayaran denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Count rooms that are active + available and not assigned to active bookings
     * that overlap on a given date.
     */
    private function getRealtimeAvailableRoomsCount(string $date): int
    {
        $bookedRoomIds = BookingDetail::query()
            ->whereIn('booking_id', function ($query) use ($date) {
                $query->select('id')
                    ->from('bookings')
                    ->where('booking_status', '!=', 'cancelled')
                    ->where('booking_status', '!=', 'checked_out')
                    ->where('check_in_date', '<=', $date)
                    ->where('check_out_date', '>', $date);
            })
            ->pluck('room_id');

        return Room::query()
            ->where('is_active', true)
            ->where('status', 'available')
            ->whereNotIn('id', $bookedRoomIds)
            ->count();
    }

    /**
     * Auto verify stale QRIS late-checkout penalty payments so queue does not require manual action.
     */
    private function autoVerifyPendingQrisLateCheckoutPenaltyPayments(): void
    {
        $pendingQrisPayments = Payment::query()
            ->where('payment_type', 'late_checkout_penalty')
            ->where('payment_method', 'qris')
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($pendingQrisPayments as $pendingPayment) {
            DB::transaction(function () use ($pendingPayment) {
                $payment = Payment::query()
                    ->where('id', $pendingPayment->id)
                    ->lockForUpdate()
                    ->first();

                if (
                    !$payment
                    || $payment->payment_type !== 'late_checkout_penalty'
                    || $payment->payment_method !== 'qris'
                    || $payment->status !== 'pending'
                ) {
                    return;
                }

                $booking = Booking::with(['bookingDetails.room'])
                    ->where('id', $payment->booking_id)
                    ->lockForUpdate()
                    ->first();

                if (!$booking) {
                    $payment->update([
                        'status' => 'rejected',
                        'verified_at' => now(),
                        'verified_by' => null,
                        'notes' => 'Ditutup otomatis karena booking tidak ditemukan.',
                    ]);
                    return;
                }

                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => null,
                    'notes' => 'Pembayaran denda checkout QRIS diverifikasi otomatis.',
                ]);

                Payment::query()
                    ->where('booking_id', $booking->id)
                    ->where('payment_type', 'late_checkout_penalty')
                    ->where('status', 'pending')
                    ->where('id', '!=', $payment->id)
                    ->update([
                        'status' => 'rejected',
                        'verified_at' => now(),
                        'verified_by' => null,
                        'notes' => 'Ditutup otomatis karena booking sudah checkout dengan pembayaran denda lain yang valid.',
                    ]);

                if ($booking->booking_status === 'checked_out') {
                    return;
                }

                if ($booking->booking_status !== 'checked_in') {
                    return;
                }

                $resolvedCheckOutAt = $booking->pending_check_out_at
                    ? $booking->pending_check_out_at->copy()
                    : now(self::HOTEL_TIMEZONE);

                $booking->update([
                    'booking_status' => 'checked_out',
                    'actual_check_out_at' => $resolvedCheckOutAt->utc(),
                    'checkout_processed_by' => null,
                    'checkout_processed_at' => now(),
                    'late_checkout_hours' => (int) $booking->pending_late_checkout_hours,
                    'late_checkout_penalty' => (float) $booking->pending_late_checkout_penalty,
                    'pending_check_out_at' => null,
                    'pending_late_checkout_hours' => 0,
                    'pending_late_checkout_penalty' => 0,
                ]);

                foreach ($booking->bookingDetails as $detail) {
                    Room::where('id', $detail->room_id)->update(['status' => 'available']);
                }
            });
        }
    }

    /**
     * Auto check-in cash dinonaktifkan. Check-in diproses manual oleh resepsionis.
     */
    private function syncCashAutoCheckInStatuses(): void
    {
        return;
    }
}
