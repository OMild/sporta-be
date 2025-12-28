<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the player home/lobby page.
     */
    public function index(Request $request)
    {
        $category = $request->get('category');

        $query = Venue::where('status', Venue::STATUS_ACTIVE);

        // Filter by category if provided
        if ($category) {
            $query->where('name', 'like', "%{$category}%")
                  ->orWhere('description', 'like', "%{$category}%")
                  ->orWhere('facilities', 'like', "%{$category}%");
        }

        $venues = $query->latest()->get();

        return view('player.home', compact('venues', 'category'));
    }

    /**
     * Show venue detail page.
     */
    public function showVenue(Venue $venue)
    {
        if (!$venue->isActive()) {
            abort(404);
        }

        return view('player.venue-detail', compact('venue'));
    }

    /**
     * Show schedule/bookings page.
     */
    public function schedule(Request $request)
    {
        $user = $request->user();
        $bookings = $user ? $user->bookings()->with('venue')->latest()->get() : collect();

        return view('player.schedule', compact('bookings'));
    }

    /**
     * Show profile page.
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return view('player.profile', compact('user'));
    }

    /**
     * Show rewards page.
     */
    public function rewards(Request $request)
    {
        return view('player.rewards');
    }
}
