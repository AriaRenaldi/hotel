<?php

namespace App\Http\Controllers;

use App\Models\HotelFacility;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    /**
     * Get all active facilities
     */
    public function getFacilities()
    {
        try {
            $facilities = HotelFacility::where('is_active', true)->get();

            return response()->json([
                'success' => true,
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all facilities (admin - including inactive)
     */
    public function getAllFacilities()
    {
        try {
            $facilities = HotelFacility::all();

            return response()->json([
                'success' => true,
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get facility detail
     */
    public function getFacilityDetail($id)
    {
        try {
            $facility = HotelFacility::find($id);

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $facility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Create facility
     */
    public function createFacility(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'facility_name' => 'required|string|max:100|unique:hotel_facilities,facility_name',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url'
            ], [
                'facility_name.unique' => 'Ada nama fasilitas yang sama.',
            ]);

            if ($validator->fails()) {
                $facilityNameError = $validator->errors()->first('facility_name');
                return response()->json([
                    'success' => false,
                    'message' => $facilityNameError ?: 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['facility_name', 'description']);

            if ($request->hasFile('image')) {
                $data['image_url'] = $request->file('image')->store('facilities', 'public');
            } elseif ($request->filled('image_url')) {
                $data['image_url'] = $request->image_url;
            }

            $facility = HotelFacility::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan',
                'data' => $facility
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Update facility
     */
    public function updateFacility(Request $request, $id)
    {
        try {
            $facility = HotelFacility::find($id);

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'facility_name' => 'sometimes|string|max:100|unique:hotel_facilities,facility_name,' . $id,
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'image_url' => 'nullable|url',
                'is_active' => 'sometimes|boolean'
            ], [
                'facility_name.unique' => 'Ada nama fasilitas yang sama.',
            ]);

            if ($validator->fails()) {
                $facilityNameError = $validator->errors()->first('facility_name');
                return response()->json([
                    'success' => false,
                    'message' => $facilityNameError ?: 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['facility_name', 'description', 'is_active']);
            $currentImagePath = $facility->getRawOriginal('image_url');

            if ($request->hasFile('image')) {
                $this->deleteStoredImageIfLocal($currentImagePath);
                $data['image_url'] = $request->file('image')->store('facilities', 'public');
            } elseif ($request->filled('image_url')) {
                if ($currentImagePath !== $request->image_url) {
                    $this->deleteStoredImageIfLocal($currentImagePath);
                }
                $data['image_url'] = $request->image_url;
            }

            $facility->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil diupdate',
                'data' => $facility->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Delete facility
     */
    public function deleteFacility($id)
    {
        try {
            $facility = HotelFacility::find($id);

            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak ditemukan'
                ], 404);
            }

            $isUsedByRoomTypes = RoomType::query()
                ->where('facilities', 'like', '%' . $facility->facility_name . '%')
                ->exists();

            if ($isUsedByRoomTypes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak bisa dihapus karena masih digunakan pada tipe kamar.'
                ], 422);
            }

            $this->deleteStoredImageIfLocal($facility->getRawOriginal('image_url'));
            $facility->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus fasilitas: ' . $e->getMessage()
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
