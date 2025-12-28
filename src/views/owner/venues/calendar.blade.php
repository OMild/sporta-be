@extends('owner.layout')

@section('title', 'Calendar - ' . $venue->name)

@section('content')
<div class="page-header">
    <div class="header-titles">
        <h1>{{ $venue->name }} - Calendar</h1>
        <p>Manage time slots and view bookings ({{ $venue->open_hour }} - {{ $venue->close_hour }})</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('owner.venues.show', $venue) }}" class="btn btn-secondary">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
            Back to Venue
        </a>
        <button id="blockTimeSlot" class="btn btn-warning">
            <svg class="icon" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M4.93 4.93l14.14 14.14"/>
            </svg>
            Block Time Slot
        </button>
    </div>
</div>

<!-- Calendar Navigation -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="?month={{ \Carbon\Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m') }}" class="btn btn-secondary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
            <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700;">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
            </h3>
            <a href="?month={{ \Carbon\Carbon::createFromFormat('Y-m', $month)->addMonth()->format('Y-m') }}" class="btn btn-secondary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </a>
        </div>
        <div style="display: flex; gap: 16px; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 16px; height: 16px; background: #10b981; border-radius: 4px;"></div>
                <span style="font-size: 0.875rem; font-weight: 500;">Available</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 16px; height: 16px; background: #3b82f6; border-radius: 4px;"></div>
                <span style="font-size: 0.875rem; font-weight: 500;">Booked</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 16px; height: 16px; background: #ef4444; border-radius: 4px;"></div>
                <span style="font-size: 0.875rem; font-weight: 500;">Blocked</span>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Grid -->
<div class="card">
    <div class="calendar-container">
        <!-- Calendar Header -->
        <div class="calendar-header">
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
        </div>
        
        <!-- Calendar Body -->
        <div class="calendar-body">
            @php
                $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();
                $currentDate = $startDate->copy()->startOfWeek();
            @endphp
            
            @while($currentDate <= $endDate->endOfWeek())
                <div class="calendar-day {{ $currentDate->format('Y-m') !== $month ? 'other-month' : '' }} {{ $currentDate->isToday() ? 'today' : '' }}" 
                     data-date="{{ $currentDate->format('Y-m-d') }}">
                    <div class="day-number">{{ $currentDate->format('j') }}</div>
                    
                    @if($currentDate->format('Y-m') === $month)
                        <div class="day-bookings">
                            @php
                                $dayBookings = $bookings->get($currentDate->format('Y-m-d'), collect());
                                $timeSlots = [];
                                
                                // Generate time slots from venue open/close hours
                                $openHour = (int) substr($venue->open_hour, 0, 2);
                                $closeHour = (int) substr($venue->close_hour, 0, 2);
                                
                                for ($hour = $openHour; $hour < $closeHour; $hour++) {
                                    $timeSlot = sprintf('%02d:00', $hour);
                                    $booking = $dayBookings->first(function($booking) use ($timeSlot) {
                                        $startHour = (int) substr($booking->start_time, 0, 2);
                                        $endHour = (int) substr($booking->end_time, 0, 2);
                                        $currentHour = (int) substr($timeSlot, 0, 2);
                                        return $currentHour >= $startHour && $currentHour < $endHour;
                                    });
                                    
                                    $status = 'available';
                                    if ($booking) {
                                        $status = $booking->status === 'blocked' ? 'blocked' : 'booked';
                                    }
                                    
                                    $timeSlots[] = [
                                        'time' => $timeSlot,
                                        'status' => $status,
                                        'booking' => $booking
                                    ];
                                }
                            @endphp
                            
                            @foreach($timeSlots as $slot)
                                <div class="time-slot time-slot-{{ $slot['status'] }}" 
                                     data-time="{{ $slot['time'] }}"
                                     data-date="{{ $currentDate->format('Y-m-d') }}"
                                     title="{{ $slot['status'] === 'blocked' ? 'Blocked' : ($slot['status'] === 'booked' ? 'Booked by ' . $slot['booking']->user->name : 'Available - Click to block') }}">
                                    <span class="time-label">{{ substr($slot['time'], 0, 5) }}</span>
                                    @if($slot['booking'])
                                        <span class="booking-info">
                                            @if($slot['status'] === 'blocked')
                                                <strong>BLOCKED</strong>
                                            @else
                                                {{ Str::limit($slot['booking']->user->name, 8) }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @php $currentDate->addDay(); @endphp
            @endwhile
        </div>
    </div>
</div>

<!-- Block Time Slot Modal -->
<div id="blockModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Block Time Slot</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="blockForm" method="POST" action="{{ route('owner.venues.block-time', $venue) }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="block_date">Date</label>
                    <input type="date" id="block_date" name="block_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <select id="start_time" name="start_time" class="form-control" required>
                        @for($hour = (int) substr($venue->open_hour, 0, 2); $hour < (int) substr($venue->close_hour, 0, 2); $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <select id="end_time" name="end_time" class="form-control" required>
                        @for($hour = (int) substr($venue->open_hour, 0, 2) + 1; $hour <= (int) substr($venue->close_hour, 0, 2); $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label for="reason">Reason (Optional)</label>
                    <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Maintenance, private event, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-warning">Block Time Slot</button>
            </div>
        </form>
    </div>
</div>

<style>
.calendar-container {
    background: #fff;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-bottom: 2px solid var(--border);
}

.calendar-day-header {
    padding: 16px 8px;
    text-align: center;
    font-weight: 600;
    color: var(--text-muted);
    background: #f8fafc;
    font-size: 0.875rem;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    min-height: 600px;
}

.calendar-day {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 8px;
    min-height: 140px;
    position: relative;
    background: #fff;
}

.calendar-day.other-month {
    background: #f8fafc;
    color: var(--text-muted);
}

.calendar-day.today {
    background: #eff6ff;
    border: 2px solid #3b82f6;
}

.day-number {
    font-weight: 700;
    margin-bottom: 8px;
    font-size: 0.875rem;
    color: var(--text-main);
}

.calendar-day.other-month .day-number {
    color: var(--text-muted);
}

.day-bookings {
    display: flex;
    flex-direction: column;
    gap: 2px;
    max-height: 100px;
    overflow-y: auto;
}

.time-slot {
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid;
    min-height: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.time-slot-available {
    background: #d1fae5;
    color: #065f46;
    border-color: #10b981;
}

.time-slot-available:hover {
    background: #a7f3d0;
    transform: scale(1.02);
}

.time-slot-booked {
    background: #dbeafe;
    color: #1e40af;
    border-color: #3b82f6;
    cursor: default;
}

.time-slot-blocked {
    background: #fee2e2;
    color: #dc2626;
    border-color: #ef4444;
    cursor: default;
}

.time-label {
    font-weight: 700;
    font-size: 0.65rem;
    line-height: 1;
}

.booking-info {
    font-size: 0.6rem;
    opacity: 0.9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    line-height: 1;
    margin-top: 1px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #fff;
    border-radius: var(--radius-lg);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--border);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #f1f5f9;
    color: var(--text-main);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 24px;
    border-top: 1px solid var(--border);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-main);
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .calendar-day {
        min-height: 100px;
        padding: 4px;
    }
    
    .day-number {
        font-size: 0.75rem;
    }
    
    .time-slot {
        font-size: 0.6rem;
        padding: 2px 4px;
        min-height: 16px;
    }
    
    .time-label {
        font-size: 0.55rem;
    }
    
    .booking-info {
        font-size: 0.5rem;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
}

/* Empty state for days with no bookings */
.calendar-day:not(.other-month) .day-bookings:empty::after {
    content: "No bookings";
    font-size: 0.7rem;
    color: var(--text-muted);
    text-align: center;
    padding: 8px 4px;
    font-style: italic;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const blockButton = document.getElementById('blockTimeSlot');
    const modal = document.getElementById('blockModal');
    const closeButtons = document.querySelectorAll('.modal-close');
    
    // Open modal
    blockButton.addEventListener('click', function() {
        modal.style.display = 'flex';
    });
    
    // Close modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });
    
    // Close modal on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Time slot click handler for available slots
    document.querySelectorAll('.time-slot-available').forEach(slot => {
        slot.addEventListener('click', function() {
            const date = this.dataset.date;
            const time = this.dataset.time;
            
            document.getElementById('block_date').value = date;
            document.getElementById('start_time').value = time;
            
            // Set end time to next hour
            const startHour = parseInt(time.split(':')[0]);
            const endTime = String(startHour + 1).padStart(2, '0') + ':00';
            document.getElementById('end_time').value = endTime;
            
            modal.style.display = 'flex';
        });
    });
    
    // Add hover effects
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('mouseenter', function() {
            if (this.classList.contains('time-slot-available')) {
                this.style.transform = 'scale(1.05)';
            }
        });
        
        slot.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection