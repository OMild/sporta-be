@extends('owner.layout')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>Dashboard Overview</h1>
        <p>Manage your venues and track performance</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.venues.create') }}" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </svg>
            Add New Venue
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Venues</span>
        </div>
        <div class="stat-value">{{ $totalVenues }}</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Active Venues</span>
        </div>
        <div class="stat-value" style="color: var(--success);">{{ $activeVenues }}</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Pending Approval</span>
        </div>
        <div class="stat-value" style="color: var(--warning);">{{ $pendingVenues }}</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Monthly Revenue</span>
        </div>
        <div class="stat-value" style="color: var(--primary);">${{ number_format($monthlyRevenue, 0) }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Today's Bookings -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 700;">Today's Bookings</h3>
        
        @if($todayBookings->count() > 0)
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($todayBookings as $booking)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-weight: 600;">{{ $booking->venue->name }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">
                                {{ $booking->user->name }} • {{ $booking->start_time }} - {{ $booking->end_time }}
                            </div>
                        </div>
                        <div>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">
                No bookings scheduled for today
            </p>
        @endif
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 700;">Recent Bookings</h3>
        
        @if($recentBookings->count() > 0)
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($recentBookings as $booking)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-weight: 600;">{{ $booking->venue->name }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">
                                {{ $booking->user->name }} • {{ $booking->booking_date->format('M d') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: var(--success);">
                                ${{ number_format($booking->total_price, 0) }}
                            </div>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">
                No recent bookings
            </p>
        @endif
    </div>
</div>

<!-- Venues Overview -->
<div class="card">
    <h3 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 700;">Your Venues</h3>
    
    @if($venues->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach($venues as $venue)
                <div style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <h4 style="font-weight: 600; font-size: 1.1rem;">{{ $venue->name }}</h4>
                        <span class="status-badge status-{{ $venue->status }}">
                            {{ ucfirst($venue->status) }}
                        </span>
                    </div>
                    
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 12px;">
                        {{ Str::limit($venue->address, 50) }}
                    </p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span style="font-weight: 600; color: var(--primary);">
                            ${{ number_format($venue->price_per_hour, 0) }}/hour
                        </span>
                        <span style="font-size: 0.875rem; color: var(--text-muted);">
                            {{ $venue->bookings->count() }} bookings
                        </span>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('owner.venues.show', $venue) }}" class="btn btn-secondary" style="flex: 1; justify-content: center; font-size: 0.875rem; padding: 8px 12px;">
                            View Details
                        </a>
                        @if($venue->isActive())
                            <a href="{{ route('owner.venues.calendar', $venue) }}" class="btn btn-primary" style="flex: 1; justify-content: center; font-size: 0.875rem; padding: 8px 12px;">
                                Calendar
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 0;">
            <svg style="width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18"/>
                <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                <path d="M5 21V10.85"/>
                <path d="M19 21V10.85"/>
                <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
            </svg>
            <h4 style="margin-bottom: 8px; color: var(--text-muted);">No venues yet</h4>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Create your first venue to start accepting bookings</p>
            <a href="{{ route('owner.venues.create') }}" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                Create Your First Venue
            </a>
        </div>
    @endif
</div>
@endsection