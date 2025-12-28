<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SPORTA - Book Your Game</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0047FF;
            --primary-light: #E8EEFF;
            --bg-main: #F5F7FA;
            --bg-card: #FFFFFF;
            --text-dark: #1A1D26;
            --text-muted: #6B7280;
            --text-light: #9CA3AF;
            --border: #E5E7EB;
            --success: #10B981;
            --warning: #F59E0B;
            --star: #F59E0B;
            --radius-full: 9999px;
            --radius-xl: 20px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow-card: 0 2px 8px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
            --shadow-nav: 0 -4px 20px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: 100px;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--bg-main);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: var(--primary);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 71, 255, 0.3);
        }

        .logo-icon svg {
            width: 28px;
            height: 28px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            font-style: italic;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .header-right {
            display: flex;
            gap: 8px;
        }

        .header-btn {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-full);
            background: var(--bg-card);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .header-btn:active {
            transform: scale(0.95);
        }

        .header-btn svg {
            width: 20px;
            height: 20px;
            color: var(--text-dark);
        }

        /* Main Content */
        .main-content {
            padding: 0 20px;
        }

        /* Section Title */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            margin-top: 24px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:active {
            transform: scale(0.97);
        }

        .filter-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Categories */
        .categories {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .categories::-webkit-scrollbar {
            display: none;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            min-width: 80px;
            text-decoration: none;
        }

        .category-icon {
            width: 72px;
            height: 72px;
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-card);
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .category-item.active .category-icon {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .category-icon:active {
            transform: scale(0.95);
        }

        .category-icon img,
        .category-icon svg {
            width: 40px;
            height: 40px;
        }

        .category-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-align: center;
        }

        .category-item.active .category-name {
            color: var(--primary);
        }

        /* Venue Cards */
        .venue-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            margin-bottom: 16px;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
        }

        .venue-card:active {
            transform: scale(0.98);
        }

        .venue-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .venue-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .venue-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 50%, #16a34a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .venue-image-placeholder svg {
            width: 64px;
            height: 64px;
            color: rgba(255, 255, 255, 0.8);
        }

        .rating-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--bg-card);
            padding: 6px 12px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .rating-badge svg {
            width: 16px;
            height: 16px;
            color: var(--star);
            fill: var(--star);
        }

        .distance-badge {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0, 0, 0, 0.7);
            padding: 8px 14px;
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .venue-info {
            padding: 16px;
        }

        .venue-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
            font-style: italic;
        }

        .venue-address {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .venue-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .venue-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
        }

        .venue-price span {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .venue-facilities {
            display: flex;
            gap: 6px;
        }

        .facility-tag {
            padding: 4px 10px;
            background: var(--bg-main);
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-card);
            display: flex;
            justify-content: space-around;
            padding: 12px 20px 28px;
            box-shadow: var(--shadow-nav);
            z-index: 1000;
            border-top: 1px solid var(--border);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            min-width: 64px;
            position: relative;
        }

        .nav-icon {
            width: 28px;
            height: 28px;
            color: var(--text-light);
            transition: all 0.2s;
        }

        .nav-item.active .nav-icon {
            color: var(--primary);
        }

        .nav-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .nav-item.active .nav-label {
            color: var(--primary);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            width: 6px;
            height: 6px;
            background: var(--primary);
            border-radius: 50%;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            color: var(--text-light);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (min-width: 768px) {
            body {
                max-width: 480px;
                margin: 0 auto;
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            }

            .bottom-nav {
                max-width: 480px;
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo-icon">
                <svg viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="gradSpeed" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#E0FF00"/>
                            <stop offset="100%" stop-color="#B8F100"/>
                        </linearGradient>
                    </defs>
                    <path d="M20 30 L55 60 L25 90" stroke="url(#gradSpeed)" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <path d="M50 30 L85 60 L55 90" stroke="url(#gradSpeed)" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <span class="brand-name">SPORTA</span>
        </div>
        <div class="header-right">
            <button class="header-btn" onclick="toggleDarkMode()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
            <button class="header-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                    <circle cx="12" cy="12" r="4"/>
                </svg>
            </button>
        </div>
    </header>

    <main class="main-content">
        <!-- Categories Section -->
        <div class="section-header">
            <h2 class="section-title">Pro Categories</h2>
            <button class="filter-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="6" x2="20" y2="6"/>
                    <line x1="8" y1="12" x2="20" y2="12"/>
                    <line x1="4" y1="18" x2="20" y2="18"/>
                    <circle cx="6" cy="6" r="2" fill="currentColor"/>
                    <circle cx="10" cy="12" r="2" fill="currentColor"/>
                    <circle cx="6" cy="18" r="2" fill="currentColor"/>
                </svg>
                Filter
            </button>
        </div>

        <div class="categories">
            <a href="{{ route('player.home') }}" class="category-item {{ !$category ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="28" stroke="#333" stroke-width="3"/>
                        <path d="M32 4 L32 60 M4 32 L60 32" stroke="#333" stroke-width="2"/>
                        <circle cx="32" cy="32" r="8" stroke="#333" stroke-width="2"/>
                    </svg>
                </div>
                <span class="category-name">All</span>
            </a>
            <a href="{{ route('player.home', ['category' => 'futsal']) }}" class="category-item {{ $category == 'futsal' ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="28" fill="#fff" stroke="#333" stroke-width="2"/>
                        <path d="M32 8 L24 20 L8 24 L12 40 L24 52 L40 52 L52 40 L56 24 L40 20 L32 8Z" fill="#333"/>
                        <circle cx="32" cy="32" r="6" fill="#fff"/>
                    </svg>
                </div>
                <span class="category-name">Futsal</span>
            </a>
            <a href="{{ route('player.home', ['category' => 'badminton']) }}" class="category-item {{ $category == 'badminton' ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <ellipse cx="44" cy="20" rx="12" ry="16" fill="#fff" stroke="#333" stroke-width="2"/>
                        <path d="M44 4 L44 36" stroke="#333" stroke-width="1.5"/>
                        <path d="M32 20 L56 20" stroke="#333" stroke-width="1.5"/>
                        <rect x="12" y="36" width="8" height="24" rx="2" fill="#8B4513"/>
                        <circle cx="16" cy="60" r="3" fill="#333"/>
                    </svg>
                </div>
                <span class="category-name">Badminton</span>
            </a>
            <a href="{{ route('player.home', ['category' => 'basketball']) }}" class="category-item {{ $category == 'basketball' ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="28" fill="#FF6B35" stroke="#333" stroke-width="2"/>
                        <path d="M4 32 C20 32 32 20 32 4" stroke="#333" stroke-width="2" fill="none"/>
                        <path d="M60 32 C44 32 32 44 32 60" stroke="#333" stroke-width="2" fill="none"/>
                        <path d="M32 4 L32 60" stroke="#333" stroke-width="2"/>
                        <path d="M4 32 L60 32" stroke="#333" stroke-width="2"/>
                    </svg>
                </div>
                <span class="category-name">Basketball</span>
            </a>
            <a href="{{ route('player.home', ['category' => 'tennis']) }}" class="category-item {{ $category == 'tennis' ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="26" fill="#CCFF00" stroke="#333" stroke-width="2"/>
                        <path d="M8 24 C20 32 20 32 8 40" stroke="#fff" stroke-width="4" fill="none"/>
                        <path d="M56 24 C44 32 44 32 56 40" stroke="#fff" stroke-width="4" fill="none"/>
                    </svg>
                </div>
                <span class="category-name">Tennis</span>
            </a>
            <a href="{{ route('player.home', ['category' => 'volleyball']) }}" class="category-item {{ $category == 'volleyball' ? 'active' : '' }}">
                <div class="category-icon">
                    <svg viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="28" fill="#fff" stroke="#333" stroke-width="2"/>
                        <path d="M32 4 Q48 20 32 32 Q16 44 32 60" stroke="#0066CC" stroke-width="3" fill="none"/>
                        <path d="M8 20 Q32 28 56 20" stroke="#FFCC00" stroke-width="3" fill="none"/>
                        <path d="M8 44 Q32 36 56 44" stroke="#CC0000" stroke-width="3" fill="none"/>
                    </svg>
                </div>
                <span class="category-name">Volleyball</span>
            </a>
        </div>

        <!-- Elite Recommendations -->
        <div class="section-header">
            <h2 class="section-title">Elite Recommendations</h2>
        </div>

        @forelse($venues as $venue)
        <a href="{{ route('player.venue', $venue) }}" class="venue-card">
            <div class="venue-image">
                <div class="venue-image-placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 15l4-4 4 4 6-6 4 4"/>
                        <circle cx="8" cy="8" r="1.5"/>
                    </svg>
                </div>
                <div class="rating-badge">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    {{ number_format(rand(40, 50) / 10, 1) }}
                </div>
                <div class="distance-badge">
                    {{ number_format(rand(5, 50) / 10, 1) }} KM Away
                </div>
            </div>
            <div class="venue-info">
                <h3 class="venue-name">{{ $venue->name }}</h3>
                <p class="venue-address">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ Str::limit($venue->address, 40) }}
                </p>
                <div class="venue-meta">
                    <div class="venue-price">
                        Rp {{ number_format($venue->price_per_hour, 0, ',', '.') }} <span>/hour</span>
                    </div>
                    @if($venue->facilities)
                    <div class="venue-facilities">
                        @foreach(array_slice(explode(',', $venue->facilities), 0, 2) as $facility)
                        <span class="facility-tag">{{ trim($facility) }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/>
                <path d="M8 15s1.5 2 4 2 4-2 4-2"/>
                <line x1="9" y1="9" x2="9.01" y2="9"/>
                <line x1="15" y1="9" x2="15.01" y2="9"/>
            </svg>
            <h3>No venues available</h3>
            <p>Check back later for new venues!</p>
        </div>
        @endforelse
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="{{ route('player.home') }}" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
            </svg>
            <span class="nav-label">Lobby</span>
        </a>
        <a href="{{ route('player.rewards') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/>
                <path d="M4 22h16"/>
                <path d="M10 22V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v14"/>
                <rect x="6" y="9" width="12" height="6" rx="1"/>
            </svg>
            <span class="nav-label">Rewards</span>
        </a>
        <a href="{{ route('player.schedule') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <rect x="7" y="14" width="3" height="3" rx="0.5"/>
                <rect x="14" y="14" width="3" height="3" rx="0.5"/>
            </svg>
            <span class="nav-label">Schedule</span>
        </a>
        <a href="{{ route('player.profile') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="nav-label">Profile</span>
        </a>
    </nav>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>
