<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — StoreHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0b0d14;
            --card: #13161f;
            --border: rgba(255,255,255,0.07);
            --accent: #6c63ff;
            --accent-hover: #5a52e0;
            --accent-soft: rgba(108,99,255,0.15);
            --text: #f0f2f8;
            --muted: #8b90a7;
            --danger: #ef4444;
            --success: #22c55e;
            --radius: 14px;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            color: var(--text);
            overflow: hidden;
        }

        /* ── Left panel ───────────────────────────────────────────────────── */
        .left-panel {
            flex: 1;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            background: linear-gradient(145deg, #0d0f1a 0%, #11142a 50%, #0b0d14 100%);
            padding: 60px;
            position: relative; overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute; top: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(108,99,255,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute; bottom: -60px; right: -40px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(56,189,248,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .brand {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 52px; position: relative; z-index: 1;
        }
        .brand-icon {
            width: 48px; height: 48px;
            background: var(--accent);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -1px;
        }
        .brand-name {
            font-size: 28px; font-weight: 800; letter-spacing: -1px;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .hero-text { position: relative; z-index: 1; text-align: center; max-width: 380px; }
        .hero-text h1 { font-size: 38px; font-weight: 800; line-height: 1.15; letter-spacing: -1.5px; margin-bottom: 16px; }
        .hero-text h1 span { color: var(--accent); }
        .hero-text p { font-size: 15px; color: var(--muted); line-height: 1.7; }

        /* floating role cards */
        .role-cards {
            display: flex; flex-direction: column; gap: 10px;
            margin-top: 40px; width: 100%; max-width: 340px;
            position: relative; z-index: 1;
        }
        .role-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px;
            padding: 12px 16px;
            display: flex; align-items: center; gap: 12px;
            animation: floatIn 0.6s ease both;
        }
        .role-card:nth-child(1) { animation-delay: 0.1s; }
        .role-card:nth-child(2) { animation-delay: 0.25s; }
        .role-card:nth-child(3) { animation-delay: 0.4s; }
        @keyframes floatIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .role-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .role-card-info h4 { font-size: 13px; font-weight: 700; margin: 0; }
        .role-card-info p  { font-size: 11.5px; color: var(--muted); margin: 0; }

        /* ── Right panel ──────────────────────────────────────────────────── */
        .right-panel {
            width: 460px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 48px;
            background: var(--card);
            border-left: 1px solid var(--border);
        }
        .login-box { width: 100%; }
        .login-box h2 { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 6px; }
        .login-box .subtitle { font-size: 14px; color: var(--muted); margin-bottom: 32px; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--muted); margin-bottom: 7px; }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 15px; pointer-events: none;
        }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px; font-family: inherit;
            padding: 11px 14px 11px 40px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.2);
            background: rgba(255,255,255,0.07);
        }
        .form-control::placeholder { color: var(--muted); }
        .form-control.is-invalid { border-color: var(--danger); }

        .toggle-password {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer; font-size: 15px;
        }
        .toggle-password:hover { color: var(--text); }

        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; }
        .remember-row input[type=checkbox] { accent-color: var(--accent); width: 15px; height: 15px; }
        .remember-row label { font-size: 13px; color: var(--muted); cursor: pointer; }

        .btn-login {
            width: 100%;
            background: var(--accent);
            border: none; border-radius: 10px;
            color: #fff; font-size: 14px; font-weight: 700; font-family: inherit;
            padding: 13px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: var(--accent-hover); }
        .btn-login:active { transform: scale(0.99); }

        .error-box {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 13px; color: #f87171;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }

        .divider { margin: 28px 0; border: none; border-top: 1px solid var(--border); }
        .demo-accounts h4 { font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: var(--muted); margin-bottom: 12px; }
        .demo-btn {
            width: 100%; background: rgba(255,255,255,0.04);
            border: 1px solid var(--border); border-radius: 8px;
            color: var(--text); font-size: 12.5px; font-weight: 600; font-family: inherit;
            padding: 9px 12px; cursor: pointer; margin-bottom: 7px;
            display: flex; align-items: center; gap: 10px;
            transition: background 0.2s, border-color 0.2s;
            text-align: left;
        }
        .demo-btn:hover { background: var(--accent-soft); border-color: var(--accent); }
        .demo-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .demo-meta { margin-left: auto; font-size: 11px; color: var(--muted); font-weight: 400; font-family: 'JetBrains Mono', monospace; }

        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; border: none; }
        }
    </style>
</head>
<body>

<!-- Left decorative panel -->
<div class="left-panel">
    <div class="brand">
        <div class="brand-icon">S</div>
        <div class="brand-name">StoreHub</div>
    </div>
    <div class="hero-text">
        <h1>Manage Your Store <span>Smarter</span></h1>
        <p>A complete store management platform with role-based access for admins, owners, and sellers.</p>
    </div>
    <div class="role-cards">
        <div class="role-card">
            <div class="role-dot" style="background:#ef4444"></div>
            <div class="role-card-info">
                <h4>Super Admin</h4>
                <p>Full system control — users, products, sales</p>
            </div>
        </div>
        <div class="role-card">
            <div class="role-dot" style="background:#f59e0b"></div>
            <div class="role-card-info">
                <h4>Shop Owner</h4>
                <p>Manage stock, revenue, sellers & categories</p>
            </div>
        </div>
        <div class="role-card">
            <div class="role-dot" style="background:#38bdf8"></div>
            <div class="role-card-info">
                <h4>Seller</h4>
                <p>View products and create sales</p>
            </div>
        </div>
    </div>
</div>

<!-- Right login panel -->
<div class="right-panel">
    <div class="login-box">
        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to your StoreHub account</p>

        @if($errors->any())
        <div class="error-box">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('success'))
        <div class="error-box" style="background:rgba(34,197,94,0.1);border-color:rgba(34,197,94,0.3);color:#4ade80">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePwd()">
                        <i class="bi bi-eye-fill" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <hr class="divider">

        <div class="demo-accounts">
            <h4>Quick demo login</h4>
            <button class="demo-btn" onclick="fillCreds('admin@store.com','Admin@1234')">
                <div class="demo-dot" style="background:#ef4444"></div>
                Super Admin
                <span class="demo-meta">admin@store.com</span>
            </button>
            <button class="demo-btn" onclick="fillCreds('owner@store.com','Owner@1234')">
                <div class="demo-dot" style="background:#f59e0b"></div>
                Shop Owner
                <span class="demo-meta">owner@store.com</span>
            </button>
            <button class="demo-btn" onclick="fillCreds('seller1@store.com','Seller@1234')">
                <div class="demo-dot" style="background:#38bdf8"></div>
                Seller
                <span class="demo-meta">seller1@store.com</span>
            </button>
        </div>
    </div>
</div>

<script>
    function fillCreds(email, password) {
        document.querySelector('input[name=email]').value = email;
        document.querySelector('input[name=password]').value = password;
    }
    function togglePwd() {
        const f = document.getElementById('passwordField');
        const i = document.getElementById('eyeIcon');
        if (f.type === 'password') {
            f.type = 'text'; i.className = 'bi bi-eye-slash-fill';
        } else {
            f.type = 'password'; i.className = 'bi bi-eye-fill';
        }
    }
</script>
</body>
</html>
