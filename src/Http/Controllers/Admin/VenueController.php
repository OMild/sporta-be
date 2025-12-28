<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Display a listing of all venues for admin management.
     */
    public function index()
    {
        $venues = Venue::with('owner')
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get stats for dashboard
        $stats = [
            'pending' => Venue::where('status', Venue::STATUS_PENDING)->count(),
            'active' => Venue::where('status', Venue::STATUS_ACTIVE)->count(),
            'suspended' => Venue::where('status', Venue::STATUS_SUSPENDED)->count(),
            'total' => Venue::count(),
        ];

        return view('admin.venues.index', compact('venues', 'stats'));
    }

    /**
     * Display pending venues for approval.
     */
    public function pending()
    {
        $pendingVenues = Venue::where('status', Venue::STATUS_PENDING)
            ->with('owner')
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.venues.pending', compact('pendingVenues'));
    }

    /**
     * Display the specified venue.
     */
    public function show(Venue $venue)
    {
        $venue->load(['owner', 'bookings' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('admin.venues.show', compact('venue'));
    }

    /**
     * Approve a pending venue.
     */
    public function approve(Venue $venue)
    {
        // Only pending venues can be approved
        if (!$venue->isPending()) {
            return redirect()->back()
                ->with('error', 'Only pending venues can be approved.');
        }

        $venue->update(['status' => Venue::STATUS_ACTIVE]);

        return redirect()->back()
            ->with('success', 'Venue approved successfully.');
    }

    /**
     * Reject a pending venue.
     */
    public function reject(Venue $venue)
    {
        // Only pending venues can be rejected
        if (!$venue->isPending()) {
            return redirect()->back()
                ->with('error', 'Only pending venues can be rejected.');
        }

        $venue->update(['status' => Venue::STATUS_SUSPENDED]);

        return redirect()->back()
            ->with('success', 'Venue rejected successfully.');
    }

    /**
     * Suspend an active venue.
     */
    public function suspend(Venue $venue)
    {
        // Only active venues can be suspended
        if (!$venue->isActive()) {
            return redirect()->back()
                ->with('error', 'Only active venues can be suspended.');
        }

        $venue->update(['status' => Venue::STATUS_SUSPENDED]);

        return redirect()->back()
            ->with('success', 'Venue suspended successfully.');
    }

    /**
     * Reactivate a suspended venue.
     */
    public function reactivate(Venue $venue)
    {
        // Only suspended venues can be reactivated
        if (!$venue->isSuspended()) {
            return redirect()->back()
                ->with('error', 'Only suspended venues can be reactivated.');
        }

        $venue->update(['status' => Venue::STATUS_ACTIVE]);

        return redirect()->back()
            ->with('success', 'Venue reactivated successfully.');
    }
}