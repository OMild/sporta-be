@extends('owner.layout')

@section('title', 'Bookings')

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>Bookings</h1>
        <p>Manage bookings for your venues</p>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('owner.bookings.index') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.875rem;">Venue</label>
                <select name="venue_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                    <option value="">All Venues</option>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>
                            {{ $venue->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.875rem;">Status</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.875rem;">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-md);">
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.875rem;">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-md);">
            </div>
            
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="font-size: 0.875rem; padding: 8px 16px;">
                    Filter
                </button>
                <a href="{{ route('owner.bookings.index') }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 8px 16px;">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card">
    @if($bookings->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Venue</th>
                    <th>Date & Time</th>
                    <th>Duration</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->user->name }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->user->email }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->venue->name }}</div>
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
        
        <!-- Pagination -->
        <div style="margin-top: 24px;">
            {{ $bookings->withQueryString()->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 0;">
            <svg style="width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
            </svg>
            <h4 style="margin-bottom: 8px; color: var(--text-muted);">No bookings found</h4>
            <p style="color: var(--text-muted);">
                @if(request()->hasAny(['status', 'venue_id', 'date_from', 'date_to']))
                    Try adjusting your filters to see more results
                @else
                    Bookings will appear here once customers start booking your venues
                @endif
            </p>
        </div>
    @endif
</div>
@endsection