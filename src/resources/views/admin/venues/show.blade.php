<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $venue->name }} - SPORTA Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .main-content {
            padding: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .breadcrumb {
            margin-bottom: 1rem;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-suspended {
            background-color: #f8d7da;
            color: #721c24;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #6c757d;
        }
        .actions-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SPORTA Admin Dashboard</h1>
        <div>
            <span>Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> > 
            <a href="{{ route('admin.venues.index') }}">Venues</a> > 
            {{ $venue->name }}
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>{{ $venue->name }}</h2>
                <span class="status-badge status-{{ $venue->status }}">
                    {{ ucfirst($venue->status) }}
                </span>
            </div>
            
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <div class="info-label">Owner</div>
                        <div class="info-value">
                            {{ $venue->owner->name }}<br>
                            <small>{{ $venue->owner->email }}</small><br>
                            <small>{{ $venue->owner->phone }}</small>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $venue->address }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Description</div>
                        <div class="info-value">{{ $venue->description }}</div>
                    </div>
                </div>
                
                <div>
                    <div class="info-item">
                        <div class="info-label">Facilities</div>
                        <div class="info-value">{{ $venue->facilities }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Operating Hours</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($venue->open_hour)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($venue->close_hour)->format('H:i') }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Price per Hour</div>
                        <div class="info-value">${{ number_format($venue->price_per_hour, 2) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Registered</div>
                        <div class="info-value">{{ $venue->created_at->format('F d, Y \a\t H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="actions-section">
                <h4>Admin Actions</h4>
                
                @if($venue->isPending())
                    <form method="POST" action="{{ route('admin.venues.approve', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this venue?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">✓ Approve Venue</button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.venues.reject', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject this venue?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">✗ Reject Venue</button>
                    </form>
                @elseif($venue->isActive())
                    <form method="POST" action="{{ route('admin.venues.suspend', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to suspend this venue?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning">⏸ Suspend Venue</button>
                    </form>
                @elseif($venue->isSuspended())
                    <form method="POST" action="{{ route('admin.venues.reactivate', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reactivate this venue?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">▶ Reactivate Venue</button>
                    </form>
                @endif
            </div>
        </div>

        @if($venue->bookings->count() > 0)
            <div class="card">
                <h3>Recent Bookings</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venue->bookings as $booking)
                            <tr>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td>${{ number_format($booking->total_price, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card">
                <h3>Bookings</h3>
                <p>No bookings found for this venue yet.</p>
            </div>
        @endif
    </div>
</body>
</html>