<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $owner->name }} - SPORTA Admin</title>
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
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .info-row {
            display: flex;
            margin-bottom: 0.5rem;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
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
        .breadcrumb {
            margin-bottom: 1rem;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
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
            <a href="{{ route('admin.owners.index') }}">Venue Owners</a> > 
            {{ $owner->name }}
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>Venue Owner Details</h2>
                <div>
                    <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('admin.owners.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $owner->name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $owner->email }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $owner->phone }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $owner->role)) }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Registered:</div>
                <div class="info-value">{{ $owner->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Total Venues:</div>
                <div class="info-value">{{ $owner->venues->count() }}</div>
            </div>
        </div>
        
        @if($owner->venues->count() > 0)
            <div class="card">
                <h3>Registered Venues</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Venue Name</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Price/Hour</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owner->venues as $venue)
                            <tr>
                                <td>{{ $venue->name }}</td>
                                <td>{{ Str::limit($venue->address, 50) }}</td>
                                <td>
                                    @if($venue->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($venue->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>{{ $venue->bookings_count }}</td>
                                <td>${{ number_format($venue->price_per_hour, 2) }}</td>
                                <td>{{ $venue->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card">
                <h3>Registered Venues</h3>
                <p>This venue owner has not registered any venues yet.</p>
            </div>
        @endif
    </div>
</body>
</html>