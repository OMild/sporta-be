@extends('owner.layout')

@section('title', $venue->name)

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>{{ $venue->name }}</h1>
        <p>Venue details and booking history</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.venues.edit', $venue) }}" class="btn btn-warning">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/>
            </svg>
            Edit Venue
        </a>
        @if($venue->isActive())
            <a href="{{ route('owner.venues.calendar', $venue) }}" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" x2="16" y1="2" y2="6"/>
                    <line x1="8" x2="8" y1="2" y2="6"/>
                    <line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                View Calendar
            </a>
        @endif
        <a href="{{ route('owner.venues.index') }}" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Venues
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Venue Details -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 700;">Venue Information</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Status</label>
                <span class="status-badge status-{{ $venue->status }}">
                    {{ ucfirst($venue->status) }}
                </span>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Price per Hour</label>
                <span style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">
                    ${{ number_format($venue->price_per_hour, 0) }}
                </span>
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Address</label>
            <p>{{ $venue->address }}</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Operating Hours</label>
                <p>{{ $venue->open_hour }} - {{ $venue->close_hour }}</p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Total Bookings</label>
                <p style="font-size: 1.25rem; font-weight: 700;">{{ $venue->bookings->count() }}</p>
            </div>
        </div>
        
        @if($venue->description)
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Description</label>
                <p>{{ $venue->description }}</p>
            </div>
        @endif
        
        @if($venue->facilities)
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-muted);">Facilities</label>
                <p>{{ $venue->facilities }}</p>
            </div>
        @endif
    </div>
    
    <!-- Quick Stats -->
    <div>
        <div class="card" style="margin-bottom: 24px;">
            <h4 style="margin-bottom: 16px; font-weight: 600;">Quick Stats</h4>
            
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">Total Revenue</span>
                    <span style="font-weight: 600;">
                        ${{ number_format($venue->bookings->where('status', '!=', 'cancelled')->sum('total_price'), 0) }}
                    </span>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">Completed Bookings</span>
                    <span style="font-weight: 600;">{{ $venue->bookings->where('status', 'completed')->count() }}</span>
                </div>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">Pending Bookings</span>
                    <span style="font-weight: 600;">{{ $venue->bookings->where('status', 'pending')->count() }}</span>
                </div>
            </div>
        </div>
        
        @if($venue->isPending())
            <div class="card" style="background: #fffbeb; border-color: #fed7aa;">
                <div style="display: flex; align-items: start; gap: 12px;">
                    <svg style="width: 20px; height: 20px; color: var(--warning); margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <div>
                        <h4 style="font-weight: 600; margin-bottom: 4px; color: var(--warning);">Pending Approval</h4>
                        <p style="color: #d97706; font-size: 0.875rem;">
                            Your venue is under review by our admin team. You'll be notified once it's approved.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Recent Bookings -->
<div class="card">
    <h3 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 700;">Recent Bookings</h3>
    
    @if($venue->bookings->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Date & Time</th>
                    <th>Duration</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venue->bookings->take(10) as $booking)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->user->name }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->user->email }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->booking_date->format('M d, Y') }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">
                                {{ $booking->start_time }} - {{ $booking->end_time }}
                            </div>
                        </td>
                        <td>
                            @php
                                $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                                $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                                $duration = $start->diffInHours($end);
                            @endphp
                            {{ $duration }} hour{{ $duration > 1 ? 's' : '' }}
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--success);">
                                ${{ number_format($booking->total_price, 0) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('owner.bookings.show', $booking) }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 6px 12px;">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($venue->bookings->count() > 10)
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('owner.bookings.index', ['venue_id' => $venue->id]) }}" class="btn btn-secondary">
                    View All Bookings
                </a>
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px 0;">
            <svg style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
            </svg>
            <h4 style="margin-bottom: 8px; color: var(--text-muted);">No bookings yet</h4>
            <p style="color: var(--text-muted);">Bookings will appear here once customers start booking your venue</p>
        </div>
    @endif
</div>
@endsection