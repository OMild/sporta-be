<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Store a newly created booking.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'venue_id' => 'required|exists:venues,id',
                'booking_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $venue = Venue::findOrFail($request->venue_id);

            // Check if venue is active
            if (!$venue->isActive()) {
                return response()->json([
                    'message' => 'Venue is not available for booking',
                    'error' => 'venue_not_active'
                ], 404);
            }

            // Validate booking time is within venue operating hours
            $startTime = Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = Carbon::createFromFormat('H:i', $request->end_time);
            
            // Handle venue hours - they might be Carbon objects or strings
            $venueOpenHour = is_string($venue->open_hour) ? $venue->open_hour : $venue->open_hour->format('H:i');
            $venueCloseHour = is_string($venue->close_hour) ? $venue->close_hour : $venue->close_hour->format('H:i');
            
            $venueOpenTime = Carbon::createFromFormat('H:i', $venueOpenHour);
            $venueCloseTime = Carbon::createFromFormat('H:i', $venueCloseHour);

            if ($startTime->lt($venueOpenTime) || $endTime->gt($venueCloseTime)) {
                return response()->json([
                    'message' => 'Booking time is outside venue operating hours',
                    'error' => 'invalid_time_slot',
                    'venue_hours' => [
                        'open' => $venueOpenHour,
                        'close' => $venueCloseHour
                    ]
                ], 422);
            }

            // Check for booking conflicts
            $conflictingBooking = $this->checkBookingConflict(
                $venue->id,
                $request->booking_date,
                $request->start_time,
                $request->end_time
            );

            if ($conflictingBooking) {
                return response()->json([
                    'message' => 'Time slot is already booked',
                    'error' => 'booking_conflict',
                    'conflicting_booking' => [
                        'id' => $conflictingBooking->id,
                        'start_time' => $conflictingBooking->start_time,
                        'end_time' => $conflictingBooking->end_time
                    ]
                ], 409);
            }

            // Calculate total price
            $duration = $startTime->diffInHours($endTime);
            $totalPrice = $venue->price_per_hour * $duration;

            // Create the booking
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'venue_id' => $request->venue_id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'total_price' => $totalPrice,
                'status' => Booking::STATUS_PENDING,
            ]);

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => [
                    'id' => $booking->id,
                    'venue_id' => $booking->venue_id,
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'total_price' => $booking->total_price,
                    'status' => $booking->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the booking',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Check for booking conflicts with existing bookings.
     *
     * @param int $venueId
     * @param string $bookingDate
     * @param string $startTime
     * @param string $endTime
     * @return Booking|null
     */
    private function checkBookingConflict(int $venueId, string $bookingDate, string $startTime, string $endTime): ?Booking
    {
        return Booking::where('venue_id', $venueId)
            ->whereDate('booking_date', $bookingDate)
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for overlapping time slots using proper overlap logic
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Existing booking starts before new booking ends AND
                    // Existing booking ends after new booking starts
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->first();
    }
}