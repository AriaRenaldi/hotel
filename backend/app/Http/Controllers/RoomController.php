<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * Get all room types
     */
    public function getRoomTypes()
    {
        try {
            $roomTypes = RoomType::where('is_active', true)->get();

            return response()->json([
                'success' => true,
                'data' => $roomTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room type detail
     */
    public function getRoomTypeDetail($id)
    {
        try {
            $roomType = RoomType::with('rooms')->find($id);

            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe kamar tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $roomType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check room availability
     */
    public function checkAvailability(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'room_type_id' => 'nullable|exists:room_types,id',
                'total_rooms' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Room::where('is_active', true)
                ->where('status', 'available')
                ->whereNotIn('id', function ($subquery) use ($request) {
                    $subquery->select('room_id')
                        ->from('booking_details')
                        ->whereIn('booking_id', function ($bookingQuery) use ($request) {
                            $bookingQuery->select('id')
                                ->from('bookings')
                                // Occupancy uses [check_in_date, check_out_date) semantics:
                                // a booking conflicts only when it starts before requested checkout
                                // and ends after requested check-in.
                                ->where('check_in_date', '<', $request->check_out)
                                ->where('check_out_date', '>', $request->check_in)
                                ->where('booking_status', '!=', 'cancelled')
                                ->where('booking_status', '!=', 'checked_out');
                        });
                });

            if ($request->room_type_id) {
                $query->where('room_type_id', $request->room_type_id);
            }

            $availableRooms = $query->with('roomType')->get();
            $totalAvailable = $availableRooms->count();

            if ($totalAvailable < $request->total_rooms) {
                return response()->json([
                    'success' => false,
                    'message' => "Kamar yang tersedia hanya {$totalAvailable} unit, tetapi Anda memesan {$request->total_rooms} unit.",
                    'data' => [
                        'available_rooms' => $totalAvailable,
                        'requested_rooms' => $request->total_rooms,
                        'rooms' => $availableRooms
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kamar tersedia',
                'data' => [
                    'available_rooms' => $totalAvailable,
                    'requested_rooms' => $request->total_rooms,
                    'rooms' => $availableRooms->take($request->total_rooms)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek ketersediaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Create room type
     */
    public function createRoomType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type_name' => 'required|string|max:50|unique:room_types',
                'description' => 'required|string',
                'price_per_night' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:1',
                'facilities' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url'
            ], [
                'type_name.unique' => 'Ada nama tipe yang sama.',
            ]);

            if ($validator->fails()) {
                $typeNameError = $validator->errors()->first('type_name');
                return response()->json([
                    'success' => false,
                    'message' => $typeNameError ?: 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only([
                'type_name',
                'description',
                'price_per_night',
                'capacity',
                'facilities',
            ]);

            if ($request->hasFile('image')) {
                $data['image_url'] = $request->file('image')->store('room-types', 'public');
            } elseif ($request->filled('image_url')) {
                $data['image_url'] = $request->image_url;
            }

            $roomType = RoomType::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe kamar berhasil dibuat',
                'data' => $roomType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Update room type
     */
    public function updateRoomType(Request $request, $id)
    {
        try {
            $roomType = RoomType::find($id);

            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe kamar tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'type_name' => 'sometimes|string|max:50|unique:room_types,type_name,' . $id,
                'description' => 'sometimes|string',
                'price_per_night' => 'sometimes|numeric|min:0',
                'capacity' => 'sometimes|integer|min:1',
                'facilities' => 'sometimes|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url',
                'is_active' => 'sometimes|boolean'
            ], [
                'type_name.unique' => 'Ada nama tipe yang sama.',
            ]);

            if ($validator->fails()) {
                $typeNameError = $validator->errors()->first('type_name');
                return response()->json([
                    'success' => false,
                    'message' => $typeNameError ?: 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only([
                'type_name',
                'description',
                'price_per_night',
                'capacity',
                'facilities',
                'is_active',
            ]);

            $currentImagePath = $roomType->getRawOriginal('image_url');

            if ($request->hasFile('image')) {
                $this->deleteStoredImageIfLocal($currentImagePath);
                $data['image_url'] = $request->file('image')->store('room-types', 'public');
            } elseif ($request->filled('image_url')) {
                if ($currentImagePath !== $request->image_url) {
                    $this->deleteStoredImageIfLocal($currentImagePath);
                }
                $data['image_url'] = $request->image_url;
            }

            $roomType->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe kamar berhasil diupdate',
                'data' => $roomType->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Create room
     */
    public function createRoom(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_number' => 'required|string|max:10|unique:rooms',
                'room_type_id' => 'required|exists:room_types,id',
                'floor' => 'nullable|integer',
                'status' => 'required|in:available,maintenance,occupied',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only([
                'room_number',
                'room_type_id',
                'floor',
                'status',
            ]);

            if ($request->hasFile('image')) {
                $data['image_url'] = $request->file('image')->store('rooms', 'public');
            } elseif ($request->filled('image_url')) {
                $data['image_url'] = $request->image_url;
            }

            $room = Room::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil dibuat',
                'data' => $room->load('roomType')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Update room
     */
    public function updateRoom(Request $request, $id)
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamar tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'room_number' => 'sometimes|string|max:10|unique:rooms,room_number,' . $id,
                'room_type_id' => 'sometimes|exists:room_types,id',
                'floor' => 'nullable|integer',
                'status' => 'sometimes|in:available,maintenance,occupied',
                'is_active' => 'sometimes|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only([
                'room_number',
                'room_type_id',
                'floor',
                'status',
                'is_active',
            ]);

            $currentImagePath = $room->getRawOriginal('image_url');

            if ($request->hasFile('image')) {
                $this->deleteStoredImageIfLocal($currentImagePath);
                $data['image_url'] = $request->file('image')->store('rooms', 'public');
            } elseif ($request->filled('image_url')) {
                if ($currentImagePath !== $request->image_url) {
                    $this->deleteStoredImageIfLocal($currentImagePath);
                }
                $data['image_url'] = $request->image_url;
            }

            $room->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil diupdate',
                'data' => $room->fresh()->load('roomType')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Get all rooms
     */
    public function getAllRooms()
    {
        try {
            $rooms = Room::with('roomType')->get();

            return response()->json([
                'success' => true,
                'data' => $rooms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllRoomTypes()
    {
        try {
            $roomTypes = RoomType::all();

            return response()->json([
                'success' => true,
                'data' => $roomTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableRooms(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

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

        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->where('is_active', true)
            ->whereNotIn('id', $bookedRoomIds)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms,
            'meta' => [
                'date' => $date,
                'total' => $rooms->count(),
            ],
        ]);
    }

    public function getOccupiedRooms()
    {
        $rooms = Room::with('roomType')->where('status', 'occupied')->get();
        return response()->json(['success' => true, 'data' => $rooms]);
    }

    public function getMaintenanceRooms()
    {
        $rooms = Room::with('roomType')->where('status', 'maintenance')->get();
        return response()->json(['success' => true, 'data' => $rooms]);
    }

    public function getRoomDetail($id)
    {
        $room = Room::with('roomType')->find($id);

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $room]);
    }

    public function updateRoomStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,maintenance,occupied'
        ]);

        $room = Room::find($id);

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }

        if ($room->status === 'occupied' && $request->status !== 'occupied') {
            return response()->json([
                'success' => false,
                'message' => 'Status kamar tidak bisa diubah karena kamar sedang terisi.'
            ], 422);
        }

        $room->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully',
            'data' => $room
        ]);
    }

    /**
     * Admin: Delete room type
     */
    public function deleteRoomType($id)
    {
        try {
            $roomType = RoomType::find($id);

            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe kamar tidak ditemukan'
                ], 404);
            }

            if ($roomType->rooms()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe kamar tidak bisa dihapus karena masih memiliki kamar (kosong maupun terisi).'
                ], 422);
            }

            $this->deleteStoredImageIfLocal($roomType->getRawOriginal('image_url'));
            $roomType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe kamar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Delete room
     */
    public function deleteRoom($id)
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamar tidak ditemukan'
                ], 404);
            }

            if ($room->status === 'occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamar yang sedang terisi tidak bisa dihapus.'
                ], 422);
            }

            if ($room->bookingDetails()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kamar yang memiliki riwayat pemesanan'
                ], 422);
            }

            $this->deleteStoredImageIfLocal($room->getRawOriginal('image_url'));
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kamar: ' . $e->getMessage()
            ], 500);
        }
    }

    private function deleteStoredImageIfLocal(?string $path): void
    {
        if (!$path || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
