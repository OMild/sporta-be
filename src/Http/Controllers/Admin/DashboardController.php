<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the financial dashboard.
     */
    public function financial()
    {
        // Calculate total platform revenue from paid bookings
        $totalRevenue = Transaction::where('type', Transaction::TYPE_IN)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        // Get recent booking transaction history (last 20 transactions)
        $recentTransactions = Transaction::with(['booking.user', 'booking.venue'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get venue owner withdrawal requests
        $withdrawalRequests = Transaction::with(['booking.venue.owner'])
            ->where('type', Transaction::TYPE_WITHDRAW)
            ->where('status', Transaction::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        // Additional financial metrics
        $totalBookings = Booking::where('status', Booking::STATUS_PAID)->count();
        $pendingWithdrawals = Transaction::where('type', Transaction::TYPE_WITHDRAW)
            ->where('status', Transaction::STATUS_PENDING)
            ->sum('amount');

        return view('admin.financial', compact(
            'totalRevenue',
            'recentTransactions',
            'withdrawalRequests',
            'totalBookings',
            'pendingWithdrawals'
        ));
    }
}