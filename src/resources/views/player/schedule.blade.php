<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Schedule - SPORTA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0047FF; --primary-light: #E8EEFF; --bg-main: #F5F7FA; --bg-card: #FFFFFF; --text-dark: #1A1D26; --text-muted: #6B7280; --border: #E5E7EB; --success: #10B981; --warning: #F59E0B; --danger: #EF4444; --radius-xl: 20px; --radius-lg: 16px; --radius-full: 9999px; --shadow-card: 0 2px 8px rgba(0,0,0,0.04); }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-dark); min-height: 100vh; padding-bottom: 100px; }

        .header { padding: 20px; background: var(--bg-main); position: sticky; top: 0; z-index: 100; }
        .header h1 { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .header p { font-size: 14px; color: var(--text-muted); }

        .content { padding: 0 20px; }

        .booking-card { background: var(--bg-card); border-radius: var(--radius-xl); padding: 16px; margin-bottom: 12px; box-shadow: var(--shadow-card); display: flex; gap: 14px; }
        .booking-image { width: 80px; height: 80px; border-radius: var(--radius-lg); background: linear-gradient(135deg, #4ade80, #22c55e); flex-shrink: 0; }
        .booking-info { flex: 1; }
        .booking-venue { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .booking-date { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
        .booking-status { display: inline-block; padding: 4px 10px; border-radius: var(--radius-full); font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #FEF3C7; color: #D97706; }
        .status-paid { background: #D1FAE5; color: #059669; }
        .status-completed { background: #E0E7FF; color: #4F46E5; }
        .status-cancelled { background: #FEE2E2; color: #DC2626; }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state svg { width: 80px; height: 80px; color: var(--text-muted); margin-bottom: 16px; opacity: 0.5; }
        .empty-state h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .empty-state p { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; }
        .empty-state a { display: inline-block; padding: 12px 24px; background: var(--primary); color: white; border-radius: var(--radius-lg); font-weight: 600; text-decoration: none; }

        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); display: flex; justify-content: space-around; padding: 12px 20px 28px; box-shadow: 0 -4px 20px rgba(0,0,0,0.08); z-index: 1000; }
        .nav-item { display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; }
        .nav-icon { width: 28px; height: 28px; color: #9CA3AF; }
        .nav-item.active .nav-icon { color: var(--primary); }
        .nav-label { font-size: 11px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; }
        .nav-item.active .nav-label { color: var(--primary); }
        .nav-item.active::after { content: ''; position: absolute; bottom: 20px; width: 6px; height: 6px; background: var(--primary); border-radius: 50%; }

        @media (min-width: 768px) { body { max-width: 480px; margin: 0 auto; } .bottom-nav { max-width: 480px; left: 50%; transform: translateX(-50%); } }
    </style>
</head>
<body>
    <header class="header">
        <h1>My Schedule</h1>
        <p>Your upcoming bookings</p>
    </header>

    <div class="content">
        @forelse($bookings as $booking)
        <div class="booking-card">
            <div class="booking-image"></div>
            <div class="booking-info">
                <div class="booking-venue">{{ $booking->venue->name ?? 'Unknown Venue' }}</div>
                <div class="booking-date">
                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('D, d M Y') }} |
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </div>
                <span class="booking-status status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3>No bookings yet</h3>
            <p>Start exploring venues and book your game!</p>
            <a href="{{ route('player.home') }}">Browse Venues</a>
        </div>
        @endforelse
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('player.home') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg><span class="nav-label">Lobby</span></a>
        <a href="{{ route('player.rewards') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 22V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v14"/><rect x="6" y="9" width="12" height="6" rx="1"/></svg><span class="nav-label">Rewards</span></a>
        <a href="{{ route('player.schedule') }}" class="nav-item active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span class="nav-label">Schedule</span></a>
        <a href="{{ route('player.profile') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="nav-label">Profile</span></a>
    </nav>
</body>
</html>
