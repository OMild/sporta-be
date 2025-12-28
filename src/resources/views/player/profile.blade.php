<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profile - SPORTA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0047FF; --primary-light: #E8EEFF; --bg-main: #F5F7FA; --bg-card: #FFFFFF; --text-dark: #1A1D26; --text-muted: #6B7280; --border: #E5E7EB; --danger: #EF4444; --radius-xl: 20px; --radius-lg: 16px; --radius-full: 9999px; --shadow-card: 0 2px 8px rgba(0,0,0,0.04); }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-dark); min-height: 100vh; padding-bottom: 100px; }

        .profile-header { background: var(--primary); padding: 40px 20px 60px; text-align: center; }
        .avatar { width: 100px; height: 100px; border-radius: 50%; background: var(--bg-card); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; border: 4px solid rgba(255,255,255,0.3); }
        .avatar svg { width: 50px; height: 50px; color: var(--text-muted); }
        .profile-name { font-size: 24px; font-weight: 800; color: white; margin-bottom: 4px; }
        .profile-email { font-size: 14px; color: rgba(255,255,255,0.8); }

        .content { padding: 0 20px; margin-top: -30px; }
        .menu-card { background: var(--bg-card); border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-card); margin-bottom: 16px; }
        .menu-item { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-bottom: 1px solid var(--border); text-decoration: none; color: var(--text-dark); }
        .menu-item:last-child { border-bottom: none; }
        .menu-icon { width: 24px; height: 24px; color: var(--primary); }
        .menu-label { flex: 1; font-size: 15px; font-weight: 600; }
        .menu-arrow { width: 20px; height: 20px; color: var(--text-muted); }

        .logout-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px; background: var(--bg-card); border: none; border-radius: var(--radius-xl); color: var(--danger); font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: var(--shadow-card); }
        .logout-btn svg { width: 20px; height: 20px; }

        .login-prompt { text-align: center; padding: 40px 20px; }
        .login-prompt h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .login-prompt p { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; }
        .login-btn { display: inline-block; padding: 14px 32px; background: var(--primary); color: white; border-radius: var(--radius-lg); font-weight: 700; text-decoration: none; }

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
    @if($user)
    <div class="profile-header">
        <div class="avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <h1 class="profile-name">{{ $user->name }}</h1>
        <p class="profile-email">{{ $user->email }}</p>
    </div>

    <div class="content">
        <div class="menu-card">
            <a href="#" class="menu-item">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="menu-label">Edit Profile</span>
                <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
            </a>
            <a href="#" class="menu-item">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span class="menu-label">Change Password</span>
                <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
            </a>
            <a href="#" class="menu-item">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="menu-label">Notifications</span>
                <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
            </a>
        </div>

        <div class="menu-card">
            <a href="#" class="menu-item">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span class="menu-label">Help & Support</span>
                <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
            </a>
            <a href="#" class="menu-item">
                <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                <span class="menu-label">Terms & Conditions</span>
                <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
            </a>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log Out
            </button>
        </form>
    </div>
    @else
    <div class="profile-header" style="padding-bottom: 40px;">
        <div class="avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
    </div>
    <div class="content">
        <div class="login-prompt" style="background: var(--bg-card); border-radius: var(--radius-xl); box-shadow: var(--shadow-card); margin-top: -30px;">
            <h3>Welcome to SPORTA</h3>
            <p>Sign in to manage your bookings and access exclusive features</p>
            <a href="{{ route('login') }}" class="login-btn">Sign In</a>
        </div>
    </div>
    @endif

    <nav class="bottom-nav">
        <a href="{{ route('player.home') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg><span class="nav-label">Lobby</span></a>
        <a href="{{ route('player.rewards') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 22V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v14"/><rect x="6" y="9" width="12" height="6" rx="1"/></svg><span class="nav-label">Rewards</span></a>
        <a href="{{ route('player.schedule') }}" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span class="nav-label">Schedule</span></a>
        <a href="{{ route('player.profile') }}" class="nav-item active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="nav-label">Profile</span></a>
    </nav>
</body>
</html>
