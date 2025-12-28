<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTA Financial Dashboard</title>
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
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .metric-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .metric-label {
            color: #666;
            margin-top: 0.5rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .nav-link {
            color: #007bff;
            text-decoration: none;
            margin-right: 1rem;
        }
        .nav-link:hover {
            text-decoration: underline;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SPORTA Financial Dashboard</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color: white;">Dashboard</a>
            <span style="color: white;">Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <!-- Financial Metrics -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">${{ number_format($totalRevenue, 2) }}</div>
                <div class="metric-label">Total Platform Revenue</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $totalBookings }}</div>
                <div class="metric-label">Paid Bookings</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${{ number_format($pendingWithdrawals, 2) }}</div>
                <div class="metric-label">Pending Withdrawals</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $withdrawalRequests->count() }}</div>
                <div class="metric-label">Withdrawal Requests</div>
            </div>
        </div>

        <!-- Withdrawal Requests -->
        <div class="card">
            <h3>Venue Owner Withdrawal Requests</h3>
            @if($withdrawalRequests->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Venue Owner</th>
                            <th>Venue</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawalRequests as $withdrawal)
                            <tr>
                                <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                                <td>{{ $withdrawal->booking->venue->owner->name ?? 'N/A' }}</td>
                                <td>{{ $withdrawal->booking->venue->name ?? 'N/A' }}</td>
                                <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $withdrawal->status }}">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No pending withdrawal requests.</p>
            @endif
        </div>

        <!-- Recent Transaction History -->
        <div class="card">
            <h3>Recent Transaction History</h3>
            @if($recentTransactions->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Venue</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->booking->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->booking->venue->name ?? 'N/A' }}</td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $transaction->status }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No recent transactions.</p>
            @endif
        </div>
    </div>
</body>
</html>