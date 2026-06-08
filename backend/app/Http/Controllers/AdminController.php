<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Payment;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class AdminController extends Controller
{
    /**
     * Admin: Dashboard with overview stats
     */
    public function dashboard(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            $thisMonth = now()->format('Y-m');
            $roomOverview = $this->getRealtimeRoomOverview($today);

            $data = [
                'total_rooms' => $roomOverview['total_rooms'],
                'available_rooms' => $roomOverview['available_rooms'],
                'occupied_rooms' => $roomOverview['occupied_rooms'],
                'maintenance_rooms' => $roomOverview['maintenance_rooms'],
                'total_room_types' => RoomType::count(),
                'total_bookings' => Booking::count(),
                'today_bookings' => Booking::whereDate('created_at', $today)->count(),
                'pending_payments' => Booking::where('payment_status', 'pending')->count(),
                'paid_payments' => Booking::where('payment_status', 'paid')->count(),
                'verified_payments' => Booking::where('payment_status', 'verified')->count(),
                'total_revenue' => Booking::where('payment_status', 'verified')->sum('total_amount'),
                'monthly_revenue' => Booking::where('payment_status', 'verified')
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'),
                'total_guests' => User::where('role', 'guest')->count(),
                'total_receptionists' => User::where('role', 'receptionist')->count(),
                'today_checkins' => Booking::whereDate('check_in_date', $today)->count(),
                'today_checkouts' => Booking::whereDate('check_out_date', $today)->count(),
                'daily_revenue_7_days' => $this->getDailyRevenue7Days(),
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
     * Admin: Get detailed stats
     */
    public function getStats(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'period' => 'nullable|in:today,week,month,year',
            ]);

            $period = $request->input('period', 'month');
            $startDate = match ($period) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'year' => now()->startOfYear(),
                default => now()->startOfMonth(),
            };

            $data = [
                'total_bookings' => Booking::where('created_at', '>=', $startDate)->count(),
                'confirmed_bookings' => Booking::where('booking_status', 'confirmed')
                    ->where('created_at', '>=', $startDate)->count(),
                'checked_in_bookings' => Booking::where('booking_status', 'checked_in')
                    ->where('created_at', '>=', $startDate)->count(),
                'checked_out_bookings' => Booking::where('booking_status', 'checked_out')
                    ->where('created_at', '>=', $startDate)->count(),
                'cancelled_bookings' => Booking::where('booking_status', 'cancelled')
                    ->where('created_at', '>=', $startDate)->count(),
                'total_revenue' => Booking::where('payment_status', 'verified')
                    ->where('created_at', '>=', $startDate)->sum('total_amount'),
                'pending_payments' => Booking::where('payment_status', 'pending')
                    ->where('created_at', '>=', $startDate)->count(),
                'occupancy_rate' => $this->calculateOccupancyRate(),
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
     * Admin: Get booking report
     */
    public function getBookingReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:confirmed,checked_in,checked_out,cancelled',
                'payment_status' => 'nullable|in:pending,paid,verified,cancelled',
            ]);

            $query = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments']);

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('check_in_date', [$request->start_date, $request->end_date]);
            }

            if ($request->status) {
                $query->where('booking_status', $request->status);
            }

            if ($request->payment_status) {
                $query->where('payment_status', $request->payment_status);
            }

            $bookings = $query->orderBy('created_at', 'desc')->get();

            $summary = [
                'total_bookings' => $bookings->count(),
                'total_amount' => $bookings->sum('total_amount'),
                'total_rooms_booked' => $bookings->sum('total_rooms'),
                'total_nights' => $bookings->sum('total_nights'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'bookings' => $bookings,
                    'summary' => $summary,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get occupancy report
     */
    public function getOccupancyReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

            $totalRooms = Room::where('is_active', true)->count();
            $roomTypes = RoomType::withCount('rooms')->get();

            $occupancyData = [];
            $currentDate = new \DateTime($startDate);
            $end = new \DateTime($endDate);

            while ($currentDate <= $end) {
                $dateStr = $currentDate->format('Y-m-d');
                $occupiedRooms = Booking::with('bookingDetails')
                    ->where('booking_status', '!=', 'cancelled')
                    ->where('check_in_date', '<=', $dateStr)
                    ->where('check_out_date', '>', $dateStr)
                    ->get()
                    ->pluck('bookingDetails')
                    ->flatten()
                    ->pluck('room_id')
                    ->unique()
                    ->count();

                $occupancyData[] = [
                    'date' => $dateStr,
                    'occupied_rooms' => $occupiedRooms,
                    'total_rooms' => $totalRooms,
                    'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0,
                ];

                $currentDate->modify('+1 day');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'total_rooms' => $totalRooms,
                    'room_types' => $roomTypes,
                    'daily_occupancy' => $occupancyData,
                    'average_occupancy_rate' => count($occupancyData) > 0
                        ? round(array_sum(array_column($occupancyData, 'occupancy_rate')) / count($occupancyData), 2)
                        : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan okupansi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get separated late checkout penalty payment report (with chart data).
     */
    public function getLateCheckoutPenaltyReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'receptionist_id' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $receptionistId = $request->filled('receptionist_id')
                ? (int) $request->input('receptionist_id')
                : null;
            $page = max(1, (int) $request->input('page', 1));
            $perPage = max(1, min(100, (int) $request->input('per_page', 6)));

            $baseQuery = Payment::query()
                ->where('payment_type', 'late_checkout_penalty')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($receptionistId) {
                $baseQuery->where('verified_by', $receptionistId);
            }

            $summaryRaw = (clone $baseQuery)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_transactions,
                    SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_transactions,
                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_transactions,
                    SUM(amount) as total_penalty_amount,
                    SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_penalty_amount
                ')
                ->first();

            $payments = (clone $baseQuery)
                ->with(['booking.guest', 'booking.bookingDetails.room.roomType', 'verifier'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $chartEndDate = $endDate->copy()->endOfDay();
            $chartStartDate = $endDate->copy()->subDays(13)->startOfDay();
            if ($startDate->gt($chartStartDate)) {
                $chartStartDate = $startDate->copy()->startOfDay();
            }

            $chartRaw = Payment::query()
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as total_transactions')
                ->selectRaw('SUM(amount) as total_amount')
                ->selectRaw('SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_amount')
                ->where('payment_type', 'late_checkout_penalty')
                ->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
            if ($receptionistId) {
                $chartRaw->where('verified_by', $receptionistId);
            }
            $chartRaw = $chartRaw
                ->groupBy('date')
                ->pluck('total_amount', 'date');

            $chartVerifiedRaw = Payment::query()
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_amount')
                ->where('payment_type', 'late_checkout_penalty')
                ->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
            if ($receptionistId) {
                $chartVerifiedRaw->where('verified_by', $receptionistId);
            }
            $chartVerifiedRaw = $chartVerifiedRaw
                ->groupBy('date')
                ->pluck('verified_amount', 'date');

            $chartCountRaw = Payment::query()
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as total_transactions')
                ->where('payment_type', 'late_checkout_penalty')
                ->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
            if ($receptionistId) {
                $chartCountRaw->where('verified_by', $receptionistId);
            }
            $chartCountRaw = $chartCountRaw
                ->groupBy('date')
                ->pluck('total_transactions', 'date');

            $dailyChart = [];
            $cursor = $chartStartDate->copy();
            while ($cursor->lte($chartEndDate)) {
                $key = $cursor->toDateString();
                $dailyChart[] = [
                    'date' => $key,
                    'total_transactions' => (int) ($chartCountRaw[$key] ?? 0),
                    'total_amount' => (float) ($chartRaw[$key] ?? 0),
                    'verified_amount' => (float) ($chartVerifiedRaw[$key] ?? 0),
                ];
                $cursor->addDay();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate->toDateString(),
                        'end_date' => $endDate->toDateString(),
                        'receptionist_id' => $receptionistId,
                    ],
                    'summary' => [
                        'total_transactions' => (int) ($summaryRaw->total_transactions ?? 0),
                        'pending_transactions' => (int) ($summaryRaw->pending_transactions ?? 0),
                        'verified_transactions' => (int) ($summaryRaw->verified_transactions ?? 0),
                        'rejected_transactions' => (int) ($summaryRaw->rejected_transactions ?? 0),
                        'total_penalty_amount' => (float) ($summaryRaw->verified_penalty_amount ?? 0),
                        'verified_penalty_amount' => (float) ($summaryRaw->verified_penalty_amount ?? 0),
                    ],
                    'chart' => $dailyChart,
                    'payments' => $payments->items(),
                    'pagination' => [
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                        'per_page' => $payments->perPage(),
                        'total' => $payments->total(),
                        'from' => $payments->firstItem(),
                        'to' => $payments->lastItem(),
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
     * Admin: Export late checkout penalty report to Excel-ready JSON.
     */
    public function exportLateCheckoutPenaltyReportExcel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'receptionist_id' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $receptionistId = $request->filled('receptionist_id')
                ? (int) $request->input('receptionist_id')
                : null;

            $payments = Payment::with(['booking.guest', 'verifier'])
                ->where('payment_type', 'late_checkout_penalty')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($receptionistId) {
                $payments->where('verified_by', $receptionistId);
            }
            $payments = $payments
                ->orderBy('created_at', 'asc')
                ->get();

            $records = $payments->map(function ($payment) {
                return [
                    'payment_id' => $payment->id,
                    'booking_number' => $payment->booking?->booking_number ?? '-',
                    'guest_name' => $payment->booking?->guest?->full_name ?? '-',
                    'payment_date' => optional($payment->created_at)->format('Y-m-d H:i:s'),
                    'amount' => (float) $payment->amount,
                    'payment_method' => strtoupper((string) $payment->payment_method),
                    'status' => (string) $payment->status,
                    'verified_by' => $payment->verifier?->full_name ?? '-',
                    'verified_at' => optional($payment->verified_at)->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => 'late_checkout_penalties',
                    'period' => [
                        'start_date' => $startDate->toDateString(),
                        'end_date' => $endDate->toDateString(),
                        'receptionist_id' => $receptionistId,
                    ],
                    'summary' => [
                        'total_transactions' => $payments->count(),
                        'pending_transactions' => $payments->where('status', 'pending')->count(),
                        'verified_transactions' => $payments->where('status', 'verified')->count(),
                        'rejected_transactions' => $payments->where('status', 'rejected')->count(),
                        'total_penalty_amount' => (float) $payments->sum('amount'),
                        'verified_penalty_amount' => (float) $payments->where('status', 'verified')->sum('amount'),
                    ],
                    'records' => $records,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor laporan pembayaran denda checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Export report to Excel format (JSON data for frontend to generate Excel)
     */
    public function exportReportExcel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'type' => 'required|in:revenue,bookings,occupancy',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bookings = Booking::with(['guest', 'bookingDetails.room.roomType', 'payments'])
                ->whereBetween('check_in_date', [$request->start_date, $request->end_date])
                ->orderBy('check_in_date', 'asc')
                ->get();

            $reportData = $bookings->map(function ($booking) {
                return [
                    'booking_number' => $booking->booking_number,
                    'guest_name' => $booking->guest->full_name ?? '-',
                    'guest_email' => $booking->guest->email ?? '-',
                    'check_in_date' => $booking->check_in_date->format('Y-m-d'),
                    'check_out_date' => $booking->check_out_date->format('Y-m-d'),
                    'total_rooms' => $booking->total_rooms,
                    'total_nights' => $booking->total_nights,
                    'subtotal' => $booking->subtotal,
                    'tax' => $booking->tax,
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => $request->type,
                    'period' => [
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ],
                    'summary' => [
                        'total_bookings' => $bookings->count(),
                        'total_revenue' => $bookings->where('payment_status', 'verified')->sum('total_amount'),
                        'total_verified' => $bookings->where('payment_status', 'verified')->count(),
                    ],
                    'records' => $reportData,
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
     * Admin: Export report to PDF format (JSON data for frontend to generate PDF)
     */
    public function exportReportPdf(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'type' => 'required|in:revenue,bookings',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bookings = Booking::with(['guest', 'bookingDetails.room.roomType'])
                ->whereBetween('check_in_date', [$request->start_date, $request->end_date])
                ->orderBy('check_in_date', 'asc')
                ->get();

            $totalRevenue = $bookings->where('payment_status', 'verified')->sum('total_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => $request->type,
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'period' => [
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ],
                    'summary' => [
                        'total_bookings' => $bookings->count(),
                        'total_revenue' => $totalRevenue,
                        'verified_bookings' => $bookings->where('payment_status', 'verified')->count(),
                        'pending_bookings' => $bookings->where('payment_status', 'pending')->count(),
                    ],
                    'bookings' => $bookings,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor laporan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get all users with pagination and filtering
     */
    public function getAllUsers(Request $request)
    {
        try {
            $query = User::select('id', 'username', 'email', 'full_name', 'phone', 'address', 'role', 'is_verified', 'photo', 'created_at');

            if ($request->role) {
                $query->where('role', $request->role);
            }

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('username', 'like', '%' . $request->search . '%');
                });
            }

            $users = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get all receptionists
     */
    public function getReceptionists(Request $request)
    {
        try {
            $receptionists = User::where('role', 'receptionist')
                ->select('id', 'username', 'email', 'full_name', 'phone', 'address', 'is_verified', 'photo', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $receptionists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data resepsionis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Create receptionist account
     */
    public function createReceptionist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users',
                'email' => 'required|email|max:100|unique:users',
                'password' => 'required|string|min:6',
                'full_name' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => 'receptionist',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Akun resepsionis berhasil dibuat',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'photo' => $user->photo,
                    'photo_url' => $user->photo_url,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun resepsionis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Update user data
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|string|max:50|unique:users,username,' . $id,
                'email' => 'sometimes|email|max:100|unique:users,email,' . $id,
                'full_name' => 'sometimes|string|max:100',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'role' => 'sometimes|in:admin,receptionist,guest',
                'is_verified' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['username', 'email', 'full_name', 'phone', 'address', 'role', 'is_verified']);

            if ($request->has('password')) {
                $validator2 = Validator::make($request->all(), [
                    'password' => 'string|min:6',
                ]);
                if ($validator2->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator2->errors()
                    ], 422);
                }
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diupdate',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role,
                    'is_verified' => $user->is_verified,
                    'photo' => $user->photo,
                    'photo_url' => $user->photo_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Delete user
     */
    public function deleteUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun admin'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Approve user (verify user)
     */
    public function approveUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $user->update([
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil disetujui',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'is_verified' => $user->is_verified,
                    'photo' => $user->photo,
                    'photo_url' => $user->photo_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Reject user
     */
    public function rejectUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $user->update([
                'is_verified' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditolak',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'is_verified' => $user->is_verified,
                    'photo' => $user->photo,
                    'photo_url' => $user->photo_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Calculate occupancy rate
     */
    private function calculateOccupancyRate()
    {
        $roomOverview = $this->getRealtimeRoomOverview(now()->toDateString());
        if ($roomOverview['total_rooms'] === 0) {
            return 0;
        }

        return round(($roomOverview['occupied_rooms'] / $roomOverview['total_rooms']) * 100, 2);
    }

    private function getRealtimeRoomOverview(string $date): array
    {
        $totalRooms = Room::where('is_active', true)->count();
        $maintenanceRoomIds = Room::where('is_active', true)
            ->where('status', 'maintenance')
            ->pluck('id');

        $occupiedByStatusRoomIds = Room::where('is_active', true)
            ->where('status', 'occupied')
            ->whereNotIn('id', $maintenanceRoomIds)
            ->pluck('id');

        $occupiedByBookingRoomIds = BookingDetail::query()
            ->whereIn('booking_id', function ($query) use ($date) {
                $query->select('id')
                    ->from('bookings')
                    ->where('booking_status', '!=', 'cancelled')
                    ->where('booking_status', '!=', 'checked_out')
                    ->where('check_in_date', '<=', $date)
                    ->where('check_out_date', '>', $date);
            })
            ->pluck('room_id')
            ->unique();

        $occupiedRoomIds = $occupiedByBookingRoomIds
            ->merge($occupiedByStatusRoomIds)
            ->unique();

        $occupiedRooms = Room::where('is_active', true)
            ->whereIn('id', $occupiedRoomIds)
            ->whereNotIn('id', $maintenanceRoomIds)
            ->count();

        $availableRooms = Room::where('is_active', true)
            ->where('status', 'available')
            ->whereNotIn('id', $maintenanceRoomIds)
            ->whereNotIn('id', $occupiedRoomIds)
            ->count();

        return [
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'available_rooms' => $availableRooms,
            'maintenance_rooms' => $maintenanceRoomIds->count(),
        ];
    }

    private function getDailyRevenue7Days(): array
    {
        $startDate = Carbon::today()->subDays(6);
        $raw = Booking::query()
            ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as total')
            ->where('payment_status', 'verified')
            ->whereDate('updated_at', '>=', $startDate->toDateString())
            ->groupBy('date')
            ->pluck('total', 'date');

        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $result[] = [
                'date' => $date,
                'total' => (float) ($raw[$date] ?? 0),
            ];
        }

        return $result;
    }
}
///////