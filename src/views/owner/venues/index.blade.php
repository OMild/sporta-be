@extends('owner.layout')

@section('title', 'My Venues')

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>My Venues</h1>
        <p>Manage your sports venues</p>
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

<div class="card">
    @if($venues->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Address</th>
                    <th>Price/Hour</th>
                    <th>Status</th>
                    <th>Bookings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venues as $venue)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $venue->name }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">
                                {{ $venue->open_hour }} - {{ $venue->close_hour }}
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 200px;">
                                {{ Str::limit($venue->address, 50) }}
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--primary);">
                                ${{ number_format($venue->price_per_hour, 0) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $venue->status }}">
                                {{ ucfirst($venue->status) }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 600;">{{ $venue->bookings_count }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('owner.venues.show', $venue) }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 6px 12px;">
                                    View
                                </a>
                                <a href="{{ route('owner.venues.edit', $venue) }}" class="btn btn-warning" style="font-size: 0.875rem; padding: 6px 12px;">
                                    Edit
                                </a>
                                @if($venue->isActive())
                                    <a href="{{ route('owner.venues.calendar', $venue) }}" class="btn btn-primary" style="font-size: 0.875rem; padding: 6px 12px;">
                                        Calendar
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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