<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for owner's venues.
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        $venueIds = $owner->venues->pluck('id');
        
        $query = Booking::whereIn('venue_id', $venueIds)
            ->with(['venue', 'user']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by venue if provided
        if ($request->has('venue_id') && $request->venue_id !== '') {
            $query->where('venue_id', $request->venue_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('booking_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('booking_date', '<=', $request->date_to);
        }
        
        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);
        
        $venues = $owner->venues;
        
        return view('owner.bookings.index', compact('bookings', 'venues'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Ensure owner can only view bookings for their venues
        if (!Auth::user()->venues->contains($booking->venue_id)) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['venue', 'user', 'transaction']);

        return view('owner.bookings.show', compact('booking'));
    }

    /**
     * Get calendar data for a specific venue.
     */
    public function calendar(Request $request, Venue $venue)
    {
        // Ensure owner can only view calendar for their venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $bookings = $venue->bookings()
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('booking_date');

        return view('owner.venues.calendar', compact('venue', 'bookings', 'month'));
    }

    /**
     * Block a time slot for a venue.
     */
    public function blockTimeSlot(Request $request, Venue $venue)
    {
        // Ensure owner can only block time slots for their venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        $request->validate([
            'block_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        // Check if there are existing bookings in this time slot
        $existingBookings = $venue->bookings()
            ->where('booking_date', $request->block_date)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->exists();

        if ($existingBookings) {
            return redirect()->back()->with('error', 'Cannot block time slot. There are existing bookings in this time period.');
        }

        // Create a blocked booking entry
        Booking::create([
            'venue_id' => $venue->id,
            'user_id' => Auth::id(), // Owner blocks it
            'booking_date' => $request->block_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => 0,
            'status' => Booking::STATUS_BLOCKED,
            'notes' => $request->reason ? 'BLOCKED: ' . $request->reason : 'BLOCKED by venue owner',
        ]);

        return redirect()->back()->with('success', 'Time slot has been successfully blocked.');
    }
}