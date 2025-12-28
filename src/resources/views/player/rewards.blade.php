<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rewards - SPORTA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0047FF; --primary-light: #E8EEFF; --bg-main: #F5F7FA; --bg-card: #FFFFFF; --text-dark: #1A1D26; --text-muted: #6B7280; --border: #E5E7EB; --gold: #F59E0B; --radius-xl: 20px; --radius-lg: 16px; --radius-full: 9999px; --shadow-card: 0 2px 8px rgba(0,0,0,0.04); }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-dark); min-height: 100vh; padding-bottom: 100px; }

        .header { padding: 20px; background: var(--bg-main); }
        .header h1 { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .header p { font-size: 14px; color: var(--text-muted); }

        .content { padding: 0 20px; }

        .points-card { background: linear-gradient(135deg, var(--primary) 0%, #0039CC 100%); border-radius: var(--radius-xl); padding: 24px; color: white; margin-bottom: 24px; }
        .points-label { font-size: 14px; opacity: 0.8; margin-bottom: 8px; }
        .points-value { font-size: 48px; font-weight: 800; margin-bottom: 4px; }
        .points-unit { font-size: 14px; opacity: 0.8; }

        .section-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--text-muted); }

        .reward-card { background: var(--bg-card); border-radius: var(--radius-xl); padding: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 16px; box-shadow: var(--shadow-card); }
        .reward-icon { width: 56px; height: 56px; border-radius: var(--radius-lg); background: var(--primary-light); display: flex; align-items: center; justify-content: center; }
        .reward-icon svg { width: 28px; height: 28px; color: var(--primary); }
        .reward-info { flex: 1; }
        .reward-name { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .reward-desc { font-size: 13px; color: var(--text-muted); }
        .reward-points { font-size: 14px; font-weight: 700; color: var(--gold); }

        .coming-soon { text-align: center; padding: 40px 20px; }
        .coming-soon svg { width: 80px; height: 80px; color: var(--gold); margin-bottom: 16px; }
        .coming-soon h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .coming-soon p { font-size: 14px; color: var(--text-muted); }

        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); display: flex; justify-content: space-around; padding: 12px 20px 28px; box-shadow: 0 -4px 20px rgba(0,0,0,0.08); z-index: 1000; }
        .nav-item { display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; }
        .nav-icon { width: 28px; height: 28px; color: #9CA3AF; }
        .nav-item.active .nav-icon { color: var(--primary); }
        .nav-label { font-size: 11px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; }
        .nav-item.active .nav-label { color: var(--primary); }

        @media (min-width: 768px) { body { max-width: 480px; margin: 0 auto; } .bottom-nav { max-width: 480px; left: 50%; transform: translateX(-50%); } }
    </style>
</head>
<body>
    <header class="header">
        <h1>Rewards</h1>
        <p>Earn points & redeem exclusive rewards</p>
    </header>

    <div class="content">
        <div class="points-card">
            <div class="points-label">Your Points</div>
            <div class="points-value">0</div>
            <div class="points-unit">SPORTA Points</div>
        </div>

        <h2 class="section-title">Available Rewards</h2>

        <div class="coming-soon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/>
                <path d="M4 22h16"/>
                <path d="M10 22V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v14"/>
                <rect x="6" y="9" width="12" height="6" rx="1"/>
            </svg>
            <h3>Coming Soon!</h3>
            <p>Exciting rewards are on the way. Stay tuned!</p>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('player.home') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg><span class="nav-label">Lobby</span></a>
        <a href="{{ route('player.rewards') }}" class="nav-item active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 22V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v14"/><rect x="6" y="9" width="12" height="6" rx="1"/></svg><span class="nav-label">Rewards</span></a>
        <a href="{{ route('player.schedule') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span class="nav-label">Schedule</span></a>
        <a href="{{ route('player.profile') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="nav-label">Profile</span></a>
    </nav>
</body>
</html>
