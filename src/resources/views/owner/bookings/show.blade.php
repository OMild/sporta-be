@extends('owner.layout')

@section('title', 'Booking Details')

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>Booking Details</h1>
        <p>View complete booking information</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.bookings.index') }}" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
            Back to Bookings
        </a>
        @if($booking->venue->isActive())
            <a href="{{ route('owner.venues.calendar', $booking->venue) }}" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" x2="16" y1="2" y2="6"/>
                    <line x1="8" x2="8" y1="2" y2="6"/>
                    <line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                View Calendar
            </a>
        @endif
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Main Booking Information -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">
                    Booking #{{ $booking->id }}
                </h2>
                <p style="color: var(--text-muted);">
                    Created {{ $booking->created_at->format('M d, Y \a\t H:i') }}
                </p>
            </div>
            <span class="status-badge status-{{ $booking->status }}" style="font-size: 0.875rem; padding: 8px 16px;">
                {{ ucfirst($booking->status) }}
            </span>
        </div>

        <!-- Booking Details Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
            <!-- Date & Time -->
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                    <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                    Date & Time
                </h3>
                <div style="space-y: 12px;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Date</label>
                        <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->booking_date->format('l, F d, Y') }}</p>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Time</label>
                        <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Duration</label>
                        <p style="font-size: 1.1rem; margin-top: 4px;">
                            @php
                                $start = \Carbon\Carbon::createFromFormat('H:i', substr($booking->start_time, 0, 5));
                                $end = \Carbon\Carbon::createFromFormat('H:i', substr($booking->end_time, 0, 5));
                                $duration = $start->diffInHours($end);
                            @endphp
                            {{ $duration }} {{ $duration == 1 ? 'hour' : 'hours' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Venue Information -->
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                    <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                        <path d="M5 21V10.85"/>
                        <path d="M19 21V10.85"/>
                        <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                    </svg>
                    Venue Details
                </h3>
                <div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Venue Name</label>
                        <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->venue->name }}</p>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Address</label>
                        <p style="font-size: 0.95rem; margin-top: 4px; line-height: 1.5;">{{ $booking->venue->address }}</p>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Facilities</label>
                        <p style="font-size: 0.95rem; margin-top: 4px;">{{ $booking->venue->facilities }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div style="border-top: 1px solid var(--border); padding-top: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Customer Information
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                <div>
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Name</label>
                    <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->user->name }}</p>
                </div>
                <div>
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Email</label>
                    <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->user->email }}</p>
                </div>
                <div>
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Phone</label>
                    <p style="font-size: 1.1rem; margin-top: 4px;">{{ $booking->user->phone ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($booking->notes)
            <div style="border-top: 1px solid var(--border); padding-top: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                    <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" x2="8" y1="13" y2="13"/>
                        <line x1="16" x2="8" y1="17" y2="17"/>
                        <polyline points="10,9 9,9 8,9"/>
                    </svg>
                    Notes
                </h3>
                <div style="background: #f8fafc; padding: 16px; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                    <p style="line-height: 1.6;">{{ $booking->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Information -->
    <div>
        <!-- Payment Information -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                    <line x1="2" x2="22" y1="10" y2="10"/>
                </svg>
                Payment Details
            </h3>
            <div style="space-y: 16px;">
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Total Amount</label>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--success); margin-top: 4px;">
                        ${{ number_format($booking->total_price, 0) }}
                    </p>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Rate per Hour</label>
                    <p style="font-size: 1.1rem; margin-top: 4px;">${{ number_format($booking->venue->price_per_hour, 0) }}</p>
                </div>
                <div>
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Payment Status</label>
                    <div style="margin-top: 8px;">
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($booking->payment_proof)
                <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px;">
                    <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Payment Proof</label>
                    <div style="margin-top: 8px;">
                        <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" x2="8" y1="13" y2="13"/>
                                <line x1="16" x2="8" y1="17" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                            View Payment Proof
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Transaction Information -->
        @if($booking->transaction)
            <div class="card" style="margin-bottom: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                    <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                        <line x1="12" x2="12" y1="2" y2="22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        <path d="M9 9l3-3 3 3"/>
                        <path d="M15 15l-3 3-3-3"/>
                    </svg>
                    Transaction
                </h3>
                <div style="space-y: 12px;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Transaction ID</label>
                        <p style="font-size: 0.95rem; margin-top: 4px; font-family: monospace;">#{{ $booking->transaction->id }}</p>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Amount</label>
                        <p style="font-size: 1.1rem; margin-top: 4px; color: var(--success);">${{ number_format($booking->transaction->amount, 0) }}</p>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Date</label>
                        <p style="font-size: 0.95rem; margin-top: 4px;">{{ $booking->transaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary);">
                <svg class="icon" style="display: inline; margin-right: 8px;" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                Quick Actions
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="mailto:{{ $booking->user->email }}" class="btn btn-secondary" style="justify-content: center;">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    Email Customer
                </a>
                
                @if($booking->user->phone)
                    <a href="tel:{{ $booking->user->phone }}" class="btn btn-secondary" style="justify-content: center;">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        Call Customer
                    </a>
                @endif

                <a href="{{ route('owner.venues.show', $booking->venue) }}" class="btn btn-primary" style="justify-content: center;">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                        <path d="M5 21V10.85"/>
                        <path d="M19 21V10.85"/>
                        <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                    </svg>
                    View Venue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection