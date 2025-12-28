<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Venue Owner Dashboard') - SPORTA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0047FF;
            --primary-hover: #0039CC;
            --bg-app: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius-lg: 16px;
            --radius-md: 12px;
            --font-sans: 'Inter', system-ui, sans-serif;
            --font-serif: 'Instrument Serif', serif;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: var(--font-sans);
            background: var(--bg-app);
            color: var(--text-main);
            height: 100vh;
            overflow: hidden;
        }

        .app-shell {
            display: flex;
            height: 100vh;
        }

        .app-sidebar {
            width: 280px;
            background: #fff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 32px 0;
        }

        .sidebar-brand {
            padding: 0 24px;
            margin-bottom: 48px;
        }

        .sidebar-brand img {
            height: 36px;
            width: auto;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 16px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 4px;
            gap: 12px;
            font-weight: 500;
            position: relative;
            text-decoration: none;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .nav-item.active {
            background: #eff6ff;
            color: var(--primary);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .sidebar-footer {
            padding: 24px 16px 0 16px;
            border-top: 1px solid var(--border);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 0 8px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #e0e7ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .user-info .user-name {
            display: block;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-info .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: #ef4444;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .logout-button:hover {
            background: #fef2f2;
        }

        .app-main {
            flex: 1;
            overflow-y: auto;
            padding: 48px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 48px;
        }

        .header-titles h1 {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .header-titles p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-warning {
            background: var(--warning);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card {
            background: #fff;
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: #fff;
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .icon {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .alert {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            border: 1px solid;
        }

        .alert-success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .alert-warning {
            background: #fffbeb;
            border-color: #fed7aa;
            color: #d97706;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-suspended {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .app-sidebar {
                width: 100%;
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .app-main {
                padding: 24px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="SPORTA">
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                    <svg class="icon" viewBox="0 0 24 24">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('owner.venues.index') }}" class="nav-item {{ request()->routeIs('owner.venues.*') ? 'active' : '' }}">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                        <path d="M5 21V10.85"/>
                        <path d="M19 21V10.85"/>
                        <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                    </svg>
                    <span>My Venues</span>
                </a>
                
                <a href="{{ route('owner.bookings.index') }}" class="nav-item {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}">
                    <svg class="icon" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                    <span>Bookings</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 2) }}</div>
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">Venue Owner</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">
                        <svg class="icon" style="width: 18px; height: 18px;" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="app-main">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>