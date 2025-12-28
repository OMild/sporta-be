<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTA Admin Dashboard</title>
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

        /* --- App Layout --- */
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

        /* --- Dashboard --- */
        .app-main {
            flex: 1;
            overflow-y: auto;
            padding: 48px;
        }

        .dashboard-header {
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

        .btn-primary, .btn-secondary {
            padding: 12px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border: none;
        }

        .btn-secondary {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        .btn-primary:hover { 
            background: var(--primary-hover); 
            transform: translateY(-1px); 
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

        .stat-growth {
            color: #10b981;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .stat-bar {
            height: 4px;
            border-radius: 2px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .action-card {
            background: #fff;
            padding: 32px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .action-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .action-icon-wrapper {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .action-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .action-list {
            list-style: none;
        }

        .action-list li {
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            color: var(--text-muted);
            font-weight: 500;
            transition: all 0.2s;
        }

        .action-list li:last-child {
            border-bottom: none;
        }

        .action-list li:hover {
            color: var(--primary);
            padding-left: 4px;
        }

        .action-list a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }

        /* SVG Icons */
        .icon {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .icon-sm {
            width: 14px;
            height: 14px;
        }

        .icon-plus {
            width: 16px;
            height: 16px;
            stroke-width: 3;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
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
            
            .actions-grid {
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
                <a href="{{ route('admin.dashboard') }}" class="nav-item active">
                    <svg class="icon" viewBox="0 0 24 24">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.venues.index') }}" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                        <path d="M5 21V10.85"/>
                        <path d="M19 21V10.85"/>
                        <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                    </svg>
                    <span>All Venues</span>
                </a>
                
                <a href="{{ route('admin.venues.pending') }}" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <line x1="12" x2="12" y1="20" y2="10"/>
                        <line x1="18" x2="18" y1="20" y2="4"/>
                        <line x1="6" x2="6" y1="20" y2="16"/>
                    </svg>
                    <span>Pending Approval</span>
                    <span class="nav-badge">{{ \App\Models\Venue::where('status', 'pending')->count() }}</span>
                </a>
                
                <a href="{{ route('admin.owners.index') }}" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Venue Owners</span>
                </a>
                
                <a href="#" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                        <path d="M5 21V10.85"/>
                        <path d="M19 21V10.85"/>
                        <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                    </svg>
                    <span>Bookings</span>
                </a>
                
                <a href="{{ route('admin.financial') }}" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <line x1="12" x2="12" y1="20" y2="10"/>
                        <line x1="18" x2="18" y1="20" y2="4"/>
                        <line x1="6" x2="6" y1="20" y2="16"/>
                    </svg>
                    <span>Financial Reports</span>
                </a>
                
                <a href="#" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>System Settings</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">SA</div>
                    <div class="user-info">
                        <span class="user-name">Super Admin</span>
                        <span class="user-role">System Master</span>
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
            <div class="dashboard-content">
                <header class="dashboard-header">
                    <div class="header-titles">
                        <h1>Dashboard Overview</h1>
                        <p>Global status and management hub</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-secondary">Export Data</button>
                        <a href="{{ route('admin.venues.pending') }}" class="btn-primary">
                            <svg class="icon-plus" viewBox="0 0 24 24">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>
                            Add New Venue
                        </a>
                    </div>
                </header>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Venues</span>
                            <span class="stat-growth">+12%</span>
                        </div>
                        <div class="stat-value">{{ \App\Models\Venue::count() }}</div>
                        <div class="stat-bar" style="background: #4F46E5; width: 40%;"></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Venue Owners</span>
                            <span class="stat-growth">+3%</span>
                        </div>
                        <div class="stat-value">{{ \App\Models\User::where('role', 'venue_owner')->count() }}</div>
                        <div class="stat-bar" style="background: #10B981; width: 40%;"></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Bookings</span>
                            <span class="stat-growth">+18%</span>
                        </div>
                        <div class="stat-value">{{ number_format(\App\Models\Booking::count()) }}</div>
                        <div class="stat-bar" style="background: #F59E0B; width: 40%;"></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Revenue</span>
                            <span class="stat-growth">+24%</span>
                        </div>
                        <div class="stat-value">${{ number_format(\App\Models\Booking::sum('total_amount'), 0) }}</div>
                        <div class="stat-bar" style="background: #6366F1; width: 40%;"></div>
                    </div>
                </div>
                
                <div class="actions-grid">
                    <div class="action-card">
                        <div class="action-header">
                            <div class="action-icon-wrapper">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <path d="M3 21h18"/>
                                    <path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/>
                                    <path d="M5 21V10.85"/>
                                    <path d="M19 21V10.85"/>
                                    <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
                                </svg>
                            </div>
                            <h3>Venue Management</h3>
                        </div>
                        <ul class="action-list">
                            <li>
                                <a href="{{ route('admin.venues.index') }}">
                                    <span>View All Venues</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.venues.pending') }}">
                                    <span>Review Pending ({{ \App\Models\Venue::where('status', 'pending')->count() }})</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Venue Analytics</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="action-card">
                        <div class="action-header">
                            <div class="action-icon-wrapper">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h3>User Management</h3>
                        </div>
                        <ul class="action-list">
                            <li>
                                <a href="{{ route('admin.owners.index') }}">
                                    <span>Manage Owners</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>View Players</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Admin Accounts</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="action-card">
                        <div class="action-header">
                            <div class="action-icon-wrapper">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                                </svg>
                            </div>
                            <h3>Booking System</h3>
                        </div>
                        <ul class="action-list">
                            <li>
                                <a href="#">
                                    <span>View All Bookings</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Disputed Bookings</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Booking Reports</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="action-card">
                        <div class="action-header">
                            <div class="action-icon-wrapper">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <line x1="12" x2="12" y1="20" y2="10"/>
                                    <line x1="18" x2="18" y1="20" y2="4"/>
                                    <line x1="6" x2="6" y1="20" y2="16"/>
                                </svg>
                            </div>
                            <h3>Financial Overview</h3>
                        </div>
                        <ul class="action-list">
                            <li>
                                <a href="{{ route('admin.financial') }}">
                                    <span>Revenue Dashboard</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Transaction History</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Generate Reports</span>
                                    <svg class="icon-sm" viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>