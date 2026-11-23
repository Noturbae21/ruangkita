<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdditionalFacility;
use App\Models\Booking;
use App\Models\BookingAdditionalFacility;
use App\Support\Clock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdditionalFacilityController extends Controller
{
    /**
     * List all additional facilities (with optional is_active filter).
     */
    public function index(Request $request): JsonResponse
    {
        $query = AdditionalFacility::query()->orderBy('id');

        if ($request->has('is_active')) {
            $isActive = filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('is_active', $isActive);
            }
        }

        return response()->json([
            'data' => $query->get(),
        ], 200);
    }

    /**
     * Create a new additional facility (Admin).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'pricing_type' => ['sometimes', 'string', Rule::in(AdditionalFacility::VALID_PRICING_TYPES)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        $facility = AdditionalFacility::create([
            'name' => trim((string) $request->input('name')),
            'description' => $request->filled('description') ? trim((string) $request->input('description')) : null,
            'price' => (int) round((float) $request->input('price')),
            'pricing_type' => strtoupper((string) $request->input('pricing_type', AdditionalFacility::PRICING_PER_BOOKING)),
            'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : true,
        ]);

        return response()->json([
            'message' => 'Fasilitas tambahan berhasil dibuat.',
            'data' => $facility,
        ], 201);
    }

    /**
     * Get details of an additional facility.
     */
    public function show(int $id): JsonResponse
    {
        $facility = AdditionalFacility::find($id);

        if (! $facility) {
            return response()->json([
                'error' => 'ADDITIONAL_FACILITY_NOT_FOUND',
                'message' => 'Fasilitas tambahan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $facility,
        ], 200);
    }

    /**
     * Update an additional facility (Admin).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $facility = AdditionalFacility::find($id);

        if (! $facility) {
            return response()->json([
                'error' => 'ADDITIONAL_FACILITY_NOT_FOUND',
                'message' => 'Fasilitas tambahan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'pricing_type' => ['sometimes', 'required', 'string', Rule::in(AdditionalFacility::VALID_PRICING_TYPES)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->has('name')) {
            $facility->name = trim((string) $request->input('name'));
        }
        if ($request->has('description')) {
            $facility->description = $request->filled('description') ? trim((string) $request->input('description')) : null;
        }
        if ($request->has('price')) {
            $facility->price = (int) round((float) $request->input('price'));
        }
        if ($request->has('pricing_type')) {
            $facility->pricing_type = strtoupper((string) $request->input('pricing_type'));
        }
        if ($request->has('is_active')) {
            $facility->is_active = (bool) $request->boolean('is_active');
        }

        $facility->save();

        return response()->json([
            'message' => 'Fasilitas tambahan berhasil diperbarui.',
            'data' => $facility,
        ], 200);
    }

    /**
     * Delete or deactivate an additional facility (Admin).
     * If used in upcoming CONFIRMED bookings, deactivate instead of deleting.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $facility = AdditionalFacility::find($id);

        if (! $facility) {
            return response()->json([
                'error' => 'ADDITIONAL_FACILITY_NOT_FOUND',
                'message' => 'Fasilitas tambahan tidak ditemukan.',
            ], 404);
        }

        $nowUtc = Clock::now($request);

        $hasUpcomingBookings = BookingAdditionalFacility::where('additional_facility_id', $facility->id)
            ->whereHas('booking', function ($query) use ($nowUtc) {
                $query->where('status', Booking::STATUS_CONFIRMED)
                    ->where('end_time', '>', $nowUtc->format('Y-m-d H:i:s'));
            })
            ->exists();

        if ($hasUpcomingBookings) {
            $facility->update(['is_active' => false]);

            return response()->json([
                'message' => 'Fasilitas tambahan masih digunakan pada booking mendatang sehingga hanya dinonaktifkan.',
                'action' => 'deactivated',
                'data' => $facility->fresh(),
            ], 200);
        }

        $facility->delete();

        return response()->json([
            'message' => 'Fasilitas tambahan berhasil dihapus.',
            'action' => 'deleted',
        ], 200);
    }
}
