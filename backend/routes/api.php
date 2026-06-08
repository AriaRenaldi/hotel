<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReseptionistController;
use App\Http\Controllers\DahboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===========================================
// PUBLIC ROUTES (No Authentication Required)
// ===========================================
Route::prefix('auth')->group(function () {
    Route::get('/otp-channels', [AuthController::class, 'otpChannels']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public Hotel Information (Guest can access without login)
Route::prefix('hotel')->group(function () {
    Route::get('/room-types', [RoomController::class, 'getRoomTypes']);
    Route::get('/room-types/{id}', [RoomController::class, 'getRoomTypeDetail']);
    Route::post('/check-availability', [RoomController::class, 'checkAvailability']);
    Route::get('/facilities', [FacilityController::class, 'getFacilities']);
    Route::post('/check-booking', [BookingController::class, 'checkBooking']);
    Route::get('/bookings/{bookingId}/print', [BookingController::class, 'printBookingPublic']);
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// ===========================================
// AUTHENTICATED ROUTES (All Users with valid token)
// ===========================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth Management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'getProfile']);
        Route::put('/', [ProfileController::class, 'updateProfile']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::post('/photo', [ProfileController::class, 'uploadPhoto']);
        Route::delete('/photo', [ProfileController::class, 'deletePhoto']);
    });

    // Dashboard (Common for all roles)
    Route::get('/dashboard/stats', [DahboardController::class, 'getStats']);
});

// ===========================================
// GUEST ONLY ROUTES (Role: guest)
// ===========================================
Route::middleware(['auth:sanctum', 'guest'])->prefix('guest')->group(function () {

    // Bookings Management
    Route::prefix('bookings')->group(function () {
        Route::post('/', [BookingController::class, 'createBooking']);
        Route::get('/my-bookings', [BookingController::class, 'getMyBookings']);
        Route::get('/{bookingId}', [BookingController::class, 'getBookingDetail']);
        Route::get('/{bookingId}/barcode', [BookingController::class, 'getBookingBarcode']);
        Route::get('/{bookingId}/print', [BookingController::class, 'printBooking']);
        Route::post('/{bookingId}/upload-proof', [BookingController::class, 'uploadPaymentProof']);
        Route::post('/{bookingId}/upload-checkin-receipt', [BookingController::class, 'uploadCheckInReceipt']);
        Route::post('/{bookingId}/check-out', [BookingController::class, 'guestCheckOut']);
        Route::get('/{bookingId}/late-checkout-penalty', [BookingController::class, 'getLateCheckoutPenaltyDetail']);
        Route::post('/{bookingId}/late-checkout-penalty/pay', [BookingController::class, 'submitLateCheckoutPenaltyPayment']);
        Route::delete('/{bookingId}', [BookingController::class, 'cancelBooking']);
    });
});

// ===========================================
// ADMIN ONLY ROUTES (Role: admin)
// ===========================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/stats', [AdminController::class, 'getStats']);

    // Room Type Management
    Route::prefix('room-types')->group(function () {
        Route::get('/', [RoomController::class, 'getAllRoomTypes']);
        Route::get('/{id}', [RoomController::class, 'getRoomTypeDetail']);
        Route::post('/', [RoomController::class, 'createRoomType']);
        Route::put('/{id}', [RoomController::class, 'updateRoomType']);
        Route::delete('/{id}', [RoomController::class, 'deleteRoomType']);
    });

    // Room Management
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'getAllRooms']);
        Route::get('/available', [RoomController::class, 'getAvailableRooms']);
        Route::get('/{id}', [RoomController::class, 'getRoomDetail']);
        Route::post('/', [RoomController::class, 'createRoom']);
        Route::put('/{id}', [RoomController::class, 'updateRoom']);
        Route::delete('/{id}', [RoomController::class, 'deleteRoom']);
        Route::patch('/{id}/status', [RoomController::class, 'updateRoomStatus']);
    });

    // Facility Management
    Route::prefix('facilities')->group(function () {
        Route::get('/', [FacilityController::class, 'getAllFacilities']);
        Route::get('/{id}', [FacilityController::class, 'getFacilityDetail']);
        Route::post('/', [FacilityController::class, 'createFacility']);
        Route::put('/{id}', [FacilityController::class, 'updateFacility']);
        Route::delete('/{id}', [FacilityController::class, 'deleteFacility']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [BookingController::class, 'downloadRevenueReport']);
        Route::get('/late-checkout-penalties', [AdminController::class, 'getLateCheckoutPenaltyReport']);
        Route::get('/late-checkout-penalties/export/excel', [AdminController::class, 'exportLateCheckoutPenaltyReportExcel']);
        Route::get('/bookings', [AdminController::class, 'getBookingReport']);
        Route::get('/occupancy', [AdminController::class, 'getOccupancyReport']);
        Route::get('/export/excel', [AdminController::class, 'exportReportExcel']);
        Route::get('/export/pdf', [AdminController::class, 'exportReportPdf']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'getAllUsers']);
        Route::get('/receptionists', [AdminController::class, 'getReceptionists']);
        Route::post('/receptionists', [AdminController::class, 'createReceptionist']);
        Route::put('/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/{id}', [AdminController::class, 'deleteUser']);
        Route::patch('/{id}/approve', [AdminController::class, 'approveUser']);
        Route::patch('/{id}/reject', [AdminController::class, 'rejectUser']);
    });
});

// ===========================================
// RECEPTIONIST ONLY ROUTES (Role: receptionist)
// ===========================================
Route::middleware(['auth:sanctum', 'receptionist'])->prefix('receptionist')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ReseptionistController::class, 'dashboard']);
    Route::get('/stats', [ReseptionistController::class, 'getStats']);

    // Bookings Management
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'getAllBookings']);
        Route::get('/today', [BookingController::class, 'getTodayBookings']);
        Route::get('/{bookingId}', [BookingController::class, 'getBookingDetail']);
        Route::put('/{bookingId}/status', [BookingController::class, 'updateBookingStatus']);
        Route::post('/{bookingId}/verify-payment', [BookingController::class, 'verifyPayment']);
        Route::post('/{bookingId}/check-in', [ReseptionistController::class, 'processCheckIn']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/daily', [BookingController::class, 'downloadDailyReport']);
        Route::get('/check-ins', [ReseptionistController::class, 'getCheckInsReport']);
        Route::get('/check-outs', [ReseptionistController::class, 'getCheckOutsReport']);
        Route::get('/late-checkout-penalties', [ReseptionistController::class, 'getLateCheckoutPenaltyReport']);
        Route::get('/export/excel', [ReseptionistController::class, 'exportReportExcel']);
    });

    // Late checkout penalty payments
    Route::prefix('late-checkout-penalties')->group(function () {
        Route::get('/verifications', [ReseptionistController::class, 'getLateCheckoutPenaltyVerificationQueue']);
        Route::post('/{paymentId}/verify', [ReseptionistController::class, 'verifyLateCheckoutPenaltyPayment']);
    });

    // Room Availability
    Route::prefix('rooms')->group(function () {
        Route::get('/available', [RoomController::class, 'getAvailableRooms']);
        Route::get('/occupied', [RoomController::class, 'getOccupiedRooms']);
        Route::get('/maintenance', [RoomController::class, 'getMaintenanceRooms']);
     
    });
});
