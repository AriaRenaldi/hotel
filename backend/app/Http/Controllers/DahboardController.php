<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DahboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            return $this->getAdminStats($user);
        } elseif ($user->role === 'receptionist') {
            return $this->getReceptionistStats($user);
        } else {
            return $this->getGuestStats($user);
        }
    }
    
    private function getAdminStats($user)
    {
        $stats = [
            'total_rooms' => DB::table('rooms')->where('is_active', true)->count(),
            'available_rooms' => DB::table('rooms')->where('is_active', true)->where('status', 'available')->count(),
            'occupied_rooms' => DB::table('rooms')->where('is_active', true)->where('status', 'occupied')->count(),
            'maintenance_rooms' => DB::table('rooms')->where('is_active', true)->where('status', 'maintenance')->count(),
            'total_bookings' => DB::table('bookings')->count(),
            'total_revenue' => DB::table('bookings')->where('payment_status', 'verified')->sum('total_amount'),
            'pending_payments' => DB::table('bookings')->where('payment_status', 'pending')->count(),
            'total_guests' => DB::table('users')->where('role', 'guest')->count(),
            'total_receptionists' => DB::table('users')->where('role', 'receptionist')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'user' => $user,
        ]);
    }
    
    private function getReceptionistStats($user)
    {
        $today = now()->format('Y-m-d');
        
        $stats = [
            'today_checkins' => DB::table('bookings')
                ->whereDate('check_in_date', $today)
                ->count(),
            'today_checkouts' => DB::table('bookings')
                ->whereDate('check_out_date', $today)
                ->count(),
            'pending_payments' => DB::table('bookings')
                ->where('payment_status', 'pending')
                ->orWhere('payment_status', 'paid')
                ->count(),
            'total_bookings_today' => DB::table('bookings')
                ->whereDate('created_at', $today)
                ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'user' => $user,
        ]);
    }
    
    private function getGuestStats($user)
    {
        $stats = [
            'my_bookings' => DB::table('bookings')
                ->where('guest_id', $user->id)
                ->count(),
            'upcoming_bookings' => DB::table('bookings')
                ->where('guest_id', $user->id)
                ->where('check_in_date', '>=', now())
                ->where('booking_status', 'confirmed')
                ->count(),
            'completed_bookings' => DB::table('bookings')
                ->where('guest_id', $user->id)
                ->where('booking_status', 'checked_out')
                ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'user' => $user,
        ]);
    }
}
