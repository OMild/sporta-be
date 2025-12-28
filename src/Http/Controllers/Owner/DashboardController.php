<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the venue owner dashboard.
     */
    public function index()
    {
        $owner = Auth::user();
        
        // Get owner's venues
        $venues = $owner->venues()->with(['bookings' => function($query) {
            $query->where('booking_date', '>=', Carbon::today())
                  ->orderBy('booking_date')
                  ->orderBy('start_time');
        }])->get();
        
        // Calculate statistics
        $totalVenues = $venues->count();
        $activeVenues = $venues->where('status', Venue::STATUS_ACTIVE)->count();
        $pendingVenues = $venues->where('status', Venue::STATUS_PENDING)->count();
        
        // Get today's bookings across all venues
        $todayBookings = Booking::whereIn('venue_id', $venues->pluck('id'))
            ->where('booking_date', Carbon::today())
            ->with(['venue', 'user'])
            ->orderBy('start_time')
            ->get();
        
        // Calculate revenue (last 30 days)
        $monthlyRevenue = Booking::whereIn('venue_id', $venues->pluck('id'))
            ->where('booking_date', '>=', Carbon::now()->subDays(30))
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->sum('total_price');
        
        // Get recent bookings (last 7 days)
        $recentBookings = Booking::whereIn('venue_id', $venues->pluck('id'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['venue', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('owner.dashboard', compact(
            'venues',
            'totalVenues',
            'activeVenues', 
            'pendingVenues',
            'todayBookings',
            'monthlyRevenue',
            'recentBookings'
        ));
    }
}