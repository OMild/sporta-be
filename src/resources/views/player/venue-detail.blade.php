<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $venue->name }} - SPORTA</title>
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
            --border: #E5E7EB;
            --success: #10B981;
            --star: #F59E0B;
            --radius-xl: 20px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-full: 9999px;
            --shadow-card: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-dark); min-height: 100vh; padding-bottom: 100px; }

        .header { display: flex; align-items: center; padding: 16px 20px; background: var(--bg-card); position: sticky; top: 0; z-index: 100; gap: 16px; }
        .back-btn { width: 40px; height: 40px; border-radius: var(--radius-full); background: var(--bg-main); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .back-btn svg { width: 20px; height: 20px; }
        .header-title { font-size: 18px; font-weight: 700; flex: 1; }

        .venue-hero { height: 250px; background: linear-gradient(135deg, #4ade80 0%, #22c55e 50%, #16a34a 100%); display: flex; align-items: center; justify-content: center; }
        .venue-hero svg { width: 80px; height: 80px; color: rgba(255,255,255,0.8); }

        .venue-content { padding: 20px; margin-top: -30px; position: relative; }
        .venue-card { background: var(--bg-card); border-radius: var(--radius-xl); padding: 20px; box-shadow: var(--shadow-card); margin-bottom: 16px; }

        .venue-name { font-size: 24px; font-weight: 800; margin-bottom: 8px; font-style: italic; }
        .venue-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
        .rating-stars { display: flex; gap: 2px; }
        .rating-stars svg { width: 18px; height: 18px; color: var(--star); fill: var(--star); }
        .rating-text { font-size: 14px; color: var(--text-muted); }

        .info-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .info-row:last-child { border-bottom: none; }
        .info-icon { width: 20px; height: 20px; color: var(--primary); flex-shrink: 0; margin-top: 2px; }
        .info-content { flex: 1; }
        .info-label { font-size: 12px; color: var(--text-muted); margin-bottom: 2px; }
        .info-value { font-size: 14px; font-weight: 600; }

        .section-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
        .facilities { display: flex; flex-wrap: wrap; gap: 8px; }
        .facility-tag { padding: 8px 14px; background: var(--primary-light); border-radius: var(--radius-full); font-size: 13px; font-weight: 600; color: var(--primary); }

        .book-bar { position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); padding: 16px 20px 32px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 -4px 20px rgba(0,0,0,0.08); }
        .price-info { }
        .price-label { font-size: 12px; color: var(--text-muted); }
        .price-value { font-size: 20px; font-weight: 800; color: var(--primary); }
        .price-value span { font-size: 14px; font-weight: 500; color: var(--text-muted); }
        .book-btn { padding: 14px 32px; background: var(--primary); color: white; border: none; border-radius: var(--radius-lg); font-size: 16px; font-weight: 700; cursor: pointer; }
        .book-btn:active { transform: scale(0.97); }

        @media (min-width: 768px) { body { max-width: 480px; margin: 0 auto; } .book-bar { max-width: 480px; left: 50%; transform: translateX(-50%); } }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('player.home') }}" class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="header-title">Venue Details</span>
    </header>

    <div class="venue-hero">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M3 15l4-4 4 4 6-6 4 4"/>
        </svg>
    </div>

    <div class="venue-content">
        <div class="venue-card">
            <h1 class="venue-name">{{ $venue->name }}</h1>
            <div class="venue-rating">
                <div class="rating-stars">
                    @for($i = 0; $i < 5; $i++)
                    <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <span class="rating-text">{{ number_format(rand(40, 50) / 10, 1) }} ({{ rand(50, 200) }} reviews)</span>
            </div>

            <div class="info-row">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $venue->address }}</div>
                </div>
            </div>

            <div class="info-row">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Operating Hours</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($venue->open_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($venue->close_hour)->format('H:i') }}</div>
                </div>
            </div>
        </div>

        @if($venue->description)
        <div class="venue-card">
            <h2 class="section-title">About</h2>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">{{ $venue->description }}</p>
        </div>
        @endif

        @if($venue->facilities)
        <div class="venue-card">
            <h2 class="section-title">Facilities</h2>
            <div class="facilities">
                @foreach(explode(',', $venue->facilities) as $facility)
                <span class="facility-tag">{{ trim($facility) }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="book-bar">
        <div class="price-info">
            <div class="price-label">Price</div>
            <div class="price-value">Rp {{ number_format($venue->price_per_hour, 0, ',', '.') }} <span>/hour</span></div>
        </div>
        <button class="book-btn">Book Now</button>
    </div>
</body>
</html>
