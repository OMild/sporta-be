@extends('owner.layout')

@section('title', 'Edit ' . $venue->name)

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>Edit Venue</h1>
        <p>Update your venue information</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.venues.show', $venue) }}" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Venue
        </a>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('owner.venues.update', $venue) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Venue Name *</label>
                <input type="text" name="name" value="{{ old('name', $venue->name) }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;"
                       placeholder="e.g., Elite Futsal Arena">
                @error('name')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Price per Hour *</label>
                <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $venue->price_per_hour) }}" required min="0" step="0.01"
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;"
                       placeholder="50.00">
                @error('price_per_hour')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Address *</label>
            <textarea name="address" required rows="3"
                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem; resize: vertical;"
                      placeholder="Full address including street, city, postal code">{{ old('address', $venue->address) }}</textarea>
            @error('address')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Description</label>
            <textarea name="description" rows="4"
                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem; resize: vertical;"
                      placeholder="Describe your venue, facilities, and what makes it special">{{ old('description', $venue->description) }}</textarea>
            @error('description')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Facilities</label>
            <textarea name="facilities" rows="3"
                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem; resize: vertical;"
                      placeholder="e.g., Parking, Locker Room, Shower, WiFi, Air Conditioning">{{ old('facilities', $venue->facilities) }}</textarea>
            @error('facilities')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Opening Hour *</label>
                <input type="time" name="open_hour" value="{{ old('open_hour', $venue->open_hour) }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;">
                @error('open_hour')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Closing Hour *</label>
                <input type="time" name="close_hour" value="{{ old('close_hour', $venue->close_hour) }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;">
                @error('close_hour')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('owner.venues.show', $venue) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 0-2-2V5a2 2 0 0 0 2-2h14a2 2 0 0 0 2 2v14a2 2 0 0 0-2 2z"/>
                    <polyline points="9,11 12,14 22,4"/>
                </svg>
                Update Venue
            </button>
        </div>
    </form>
</div>
@endsection