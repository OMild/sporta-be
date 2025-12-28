<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Venues - SPORTA Admin</title>
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
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
        .venue-details {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
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
            Pending Approval
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
                <h2>Pending Venues for Approval</h2>
                <a href="{{ route('admin.venues.index') }}" class="btn btn-primary">View All Venues</a>
            </div>
            
            @if($pendingVenues->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Venue Name</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Price/Hour</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVenues as $venue)
                            <tr>
                                <td>
                                    <strong>{{ $venue->name }}</strong>
                                    <div class="venue-details">
                                        {{ strlen($venue->description) > 50 ? substr($venue->description, 0, 50) . '...' : $venue->description }}
                                    </div>
                                </td>
                                <td>
                                    {{ $venue->owner->name }}
                                    <div class="venue-details">
                                        {{ $venue->owner->email }}
                                    </div>
                                </td>
                                <td>{{ strlen($venue->address) > 40 ? substr($venue->address, 0, 40) . '...' : $venue->address }}</td>
                                <td>${{ number_format($venue->price_per_hour, 2) }}</td>
                                <td>
                                    <span class="status-badge status-pending">
                                        {{ ucfirst($venue->status) }}
                                    </span>
                                </td>
                                <td>{{ $venue->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.venues.show', $venue) }}" class="btn btn-primary">View</a>
                                    
                                    <form method="POST" action="{{ route('admin.venues.approve', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this venue?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.venues.reject', $venue) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject this venue?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="pagination">
                    {{ $pendingVenues->links() }}
                </div>
            @else
                <p>No pending venues found. All venues have been reviewed!</p>
            @endif
        </div>
    </div>
</body>
</html>