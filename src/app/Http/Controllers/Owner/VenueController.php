<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VenueController extends Controller
{
    /**
     * Display a listing of the owner's venues.
     */
    public function index()
    {
        $venues = Auth::user()->venues()
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('owner.venues.index', compact('venues'));
    }

    /**
     * Show the form for creating a new venue.
     */
    public function create()
    {
        return view('owner.venues.create');
    }

    /**
     * Store a newly created venue in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'open_hour' => 'required|date_format:H:i',
            'close_hour' => 'required|date_format:H:i|after:open_hour',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = Venue::STATUS_PENDING; // Always pending for new venues

        Venue::create($validated);

        return redirect()->route('owner.venues.index')
            ->with('success', 'Venue created successfully! It will be reviewed by admin before going live.');
    }

    /**
     * Display the specified venue.
     */
    public function show(Venue $venue)
    {
        // Ensure owner can only view their own venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        $venue->load(['bookings' => function($query) {
            $query->with('user')
                  ->orderBy('booking_date', 'desc')
                  ->orderBy('start_time', 'desc');
        }]);

        return view('owner.venues.show', compact('venue'));
    }

    /**
     * Show the form for editing the specified venue.
     */
    public function edit(Venue $venue)
    {
        // Ensure owner can only edit their own venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        return view('owner.venues.edit', compact('venue'));
    }

    /**
     * Update the specified venue in storage.
     */
    public function update(Request $request, Venue $venue)
    {
        // Ensure owner can only update their own venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'open_hour' => 'required|date_format:H:i',
            'close_hour' => 'required|date_format:H:i|after:open_hour',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        $venue->update($validated);

        return redirect()->route('owner.venues.show', $venue)
            ->with('success', 'Venue updated successfully!');
    }

    /**
     * Remove the specified venue from storage.
     */
    public function destroy(Venue $venue)
    {
        // Ensure owner can only delete their own venues
        if ($venue->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue.');
        }

        // Check if venue has any bookings
        if ($venue->bookings()->count() > 0) {
            return redirect()->route('owner.venues.index')
                ->with('error', 'Cannot delete venue with existing bookings.');
        }

        $venue->delete();

        return redirect()->route('owner.venues.index')
            ->with('success', 'Venue deleted successfully!');
    }
}