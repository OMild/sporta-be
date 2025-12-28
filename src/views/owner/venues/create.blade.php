@extends('owner.layout')

@section('title', 'Add New Venue')

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>Add New Venue</h1>
        <p>Create a new sports venue for booking</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.venues.index') }}" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Venues
        </a>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('owner.venues.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Venue Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;"
                       placeholder="e.g., Elite Futsal Arena">
                @error('name')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Price per Hour *</label>
                <input type="number" name="price_per_hour" value="{{ old('price_per_hour') }}" required min="0" step="0.01"
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
                      placeholder="Full address including street, city, postal code">{{ old('address') }}</textarea>
            @error('address')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Description</label>
            <textarea name="description" rows="4"
                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem; resize: vertical;"
                      placeholder="Describe your venue, facilities, and what makes it special">{{ old('description') }}</textarea>
            @error('description')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Facilities</label>
            <textarea name="facilities" rows="3"
                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem; resize: vertical;"
                      placeholder="e.g., Parking, Locker Room, Shower, WiFi, Air Conditioning">{{ old('facilities') }}</textarea>
            @error('facilities')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Opening Hour *</label>
                <input type="time" name="open_hour" value="{{ old('open_hour', '06:00') }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;">
                @error('open_hour')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Closing Hour *</label>
                <input type="time" name="close_hour" value="{{ old('close_hour', '22:00') }}" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 1rem;">
                @error('close_hour')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="background: #f8fafc; padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px;">
            <div style="display: flex; align-items: start; gap: 12px;">
                <svg style="width: 20px; height: 20px; color: var(--primary); margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 4px;">Review Process</h4>
                    <p style="color: var(--text-muted); font-size: 0.875rem;">
                        Your venue will be submitted for admin review and approval before it goes live. 
                        This usually takes 1-2 business days. You'll be notified once it's approved.
                    </p>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('owner.venues.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                Create Venue
            </button>
        </div>
    </form>
</div>
@endsection