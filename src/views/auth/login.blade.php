<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTA Admin Login</title>
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

        .login-container {
            display: flex;
            height: 100vh;
            background: #fff;
        }

        .login-visual {
            flex: 1.2;
            background: #1e1b4b;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px;
            overflow: hidden;
        }

        .visual-content {
            position: relative;
            z-index: 10;
            color: #fff;
        }

        .brand-logo {
            margin-bottom: 20px;
        }

        .brand-logo img {
            height: 60px;
            width: auto;
        }

        .visual-tagline {
            font-size: 1.25rem;
            opacity: 0.8;
            max-width: 400px;
            line-height: 1.5;
        }

        .visual-blob {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, transparent 70%);
            bottom: -200px;
            right: -200px;
            border-radius: 50%;
        }

        .login-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: all 0.2s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 0.875rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        .error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            font-weight: 500;
        }

        .error-container {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        .error-container .error {
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-visual {
                flex: none;
                height: 40vh;
                padding: 40px;
            }
            
            .brand-logo img {
                height: 40px;
            }
            
            .visual-tagline {
                font-size: 1rem;
            }
            
            .login-form-side {
                flex: 1;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Visual Side -->
        <div class="login-visual">
            <div class="visual-content">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo.svg') }}" alt="SPORTA Logo">
                </div>
                <p class="visual-tagline">Manage the world's finest sports venues with precision.</p>
            </div>
            <div class="visual-blob"></div>
        </div>
        
        <!-- Form Side -->
        <div class="login-form-side">
            <form class="login-box" method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="form-header">
                    <h1>Welcome Back</h1>
                    <p>Admin Portal Access</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="error-container">
                        @foreach ($errors->all() as $error)
                            <div class="error">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="admin@sporta.com" 
                        value="{{ old('email') }}" 
                        required 
                    />
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required 
                    />
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" />
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button class="login-btn" type="submit" id="submitBtn">
                    <span id="btnText">Sign In to Dashboard</span>
                    <div id="btnSpinner" class="spinner" style="display: none;"></div>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnSpinner.style.display = 'block';
            
            // Note: Form will submit normally, loading state is just for UX
        });
    </script>
</body>
</html>