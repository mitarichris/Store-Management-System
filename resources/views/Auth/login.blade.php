<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — StoreHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #080a12;
            --card: #0f1219;
            --border: rgba(255,255,255,0.06);
            --accent: #6c63ff;
            --accent-hover: #5a52e0;
            --accent-glow: rgba(108,99,255,0.4);
            --accent-soft: rgba(108,99,255,0.12);
            --text: #f0f2f8;
            --muted: #7a7f9a;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
            --info: #38bdf8;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            color: var(--text);
            overflow: hidden;
        }

        /* ── Language Switcher ─────────────────────────────────────────── */
        .lang-switch {
            position: fixed; top: 18px; right: 20px; z-index: 100;
        }
        .lang-btn {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 8px 14px;
            color: var(--text); font-size: 13px; font-weight: 600;
            font-family: inherit; cursor: pointer;
            backdrop-filter: blur(12px);
            transition: all 0.2s ease;
        }
        .lang-btn:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
        }
        .lang-btn i { font-size: 16px; color: var(--accent); }
        .lang-btn .lang-arrow {
            font-size: 10px; color: var(--muted);
            transition: transform 0.2s ease;
        }
        .lang-switch.open .lang-btn .lang-arrow { transform: rotate(180deg); }

        .lang-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            background: #161923;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 6px;
            width: 220px;
            max-height: 340px;
            overflow-y: auto;
            box-shadow: 0 16px 48px rgba(0,0,0,0.5);
            opacity: 0; visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s ease;
        }
        .lang-switch.open .lang-dropdown {
            opacity: 1; visibility: visible;
            transform: translateY(0);
        }
        .lang-dropdown::-webkit-scrollbar { width: 5px; }
        .lang-dropdown::-webkit-scrollbar-track { background: transparent; }
        .lang-dropdown::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .lang-option {
            display: flex; align-items: center; gap: 10px;
            width: 100%; background: none; border: none;
            padding: 9px 12px; border-radius: 8px;
            color: var(--text); font-size: 13px; font-weight: 500;
            font-family: inherit; cursor: pointer;
            transition: background 0.15s ease;
        }
        .lang-option:hover { background: rgba(255,255,255,0.05); }
        .lang-option.active {
            background: var(--accent-soft);
            color: var(--accent);
        }
        .lang-option .flag { font-size: 18px; line-height: 1; }
        .lang-option .lang-name { flex: 1; text-align: left; }
        .lang-option .lang-check {
            font-size: 14px; color: var(--accent);
            opacity: 0; transition: opacity 0.15s;
        }
        .lang-option.active .lang-check { opacity: 1; }

        /* ── Left Panel ──────────────────────────────────────────────────── */
        .left-panel {
            flex: 1;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 50px 40px;
            position: relative; overflow: hidden;
            background: linear-gradient(160deg, #0a0c18 0%, #0e1025 40%, #121530 70%, #0a0c18 100%);
        }
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none;
            animation: orbFloat 8s ease-in-out infinite alternate;
        }
        .orb-1 { width: 350px; height: 350px; top: -100px; left: -80px; background: rgba(108,99,255,0.2); animation-delay: 0s; }
        .orb-2 { width: 250px; height: 250px; bottom: -60px; right: -40px; background: rgba(56,189,248,0.12); animation-delay: -3s; }
        .orb-3 { width: 180px; height: 180px; top: 40%; left: 50%; background: rgba(34,197,94,0.08); animation-delay: -5s; }
        @keyframes orbFloat {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(20px, -15px) scale(1.05); }
            100% { transform: translate(-10px, 10px) scale(0.95); }
        }
        .grid-dots {
            position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Floating stat cards */
        .float-cards { position: absolute; inset: 0; pointer-events: none; z-index: 1; }
        .float-card {
            position: absolute;
            background: rgba(15,18,25,0.8);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px; padding: 14px 18px;
            backdrop-filter: blur(12px);
            display: flex; align-items: center; gap: 12px;
            animation: floatCard 6s ease-in-out infinite alternate;
        }
        .float-card:nth-child(1) { top: 12%; left: 8%; animation-delay: 0s; }
        .float-card:nth-child(2) { top: 55%; right: 6%; animation-delay: -2s; }
        .float-card:nth-child(3) { bottom: 14%; left: 12%; animation-delay: -4s; }
        @keyframes floatCard { 0% { transform: translateY(0); } 100% { transform: translateY(-8px); } }
        .fc-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .fc-info .fc-val { font-size: 16px; font-weight: 800; line-height: 1.2; }
        .fc-info .fc-lbl { font-size: 10.5px; color: var(--muted); font-weight: 500; }

        /* Brand */
        .brand {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 44px; position: relative; z-index: 2;
        }
        .brand-icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--accent) 0%, #8b5cf6 100%);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -1px;
            box-shadow: 0 8px 32px rgba(108,99,255,0.35);
        }
        .brand-name {
            font-size: 30px; font-weight: 800; letter-spacing: -1px;
            background: linear-gradient(135deg, #fff 0%, #a78bfa 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* Hero text */
        .hero-text { position: relative; z-index: 2; text-align: center; max-width: 420px; }
        .hero-text h1 { font-size: 40px; font-weight: 800; line-height: 1.12; letter-spacing: -1.5px; margin-bottom: 16px; }
        .hero-text h1 .gradient {
            background: linear-gradient(135deg, var(--info) 0%, var(--accent) 50%, #a78bfa 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .hero-text p { font-size: 15px; color: var(--muted); line-height: 1.75; }

        /* Feature pills */
        .features {
            display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;
            margin-top: 36px; position: relative; z-index: 2;
        }
        .feature-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px; padding: 7px 14px;
            font-size: 12px; font-weight: 500; color: var(--muted);
            animation: pillIn 0.5s ease both;
        }
        .feature-pill:nth-child(1) { animation-delay: 0.1s; }
        .feature-pill:nth-child(2) { animation-delay: 0.2s; }
        .feature-pill:nth-child(3) { animation-delay: 0.3s; }
        .feature-pill:nth-child(4) { animation-delay: 0.4s; }
        @keyframes pillIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .feature-pill i { font-size: 13px; }

        /* ── Right panel ──────────────────────────────────────────────── */
        .right-panel {
            width: 480px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 48px;
            background: linear-gradient(180deg, #0d1017 0%, #0f1219 50%, #0d1017 100%);
            border-left: 1px solid var(--border);
            position: relative;
        }
        .right-panel::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 200px;
            background: linear-gradient(180deg, rgba(108,99,255,0.04) 0%, transparent 100%);
            pointer-events: none;
        }
        .login-box { width: 100%; position: relative; z-index: 1; }
        .login-box .logo-mobile { display: none; align-items: center; gap: 10px; margin-bottom: 28px; }
        .login-box .logo-mobile .brand-icon { width: 40px; height: 40px; font-size: 18px; border-radius: 10px; box-shadow: 0 6px 24px rgba(108,99,255,0.3); }
        .login-box .logo-mobile .brand-name { font-size: 22px; }
        .login-box h2 { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 6px; }
        .login-box .subtitle { font-size: 14px; color: var(--muted); margin-bottom: 32px; }

        /* ── Form ──────────────────────────────────────────────────────── */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--muted); margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 15px; pointer-events: none; transition: color 0.2s;
        }
        .input-wrap:focus-within i { color: var(--accent); }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 12px; color: var(--text);
            font-size: 14px; font-family: inherit;
            padding: 12px 14px 12px 42px; outline: none;
            transition: all 0.25s ease;
        }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(108,99,255,0.12); background: rgba(255,255,255,0.05); }
        .form-control::placeholder { color: rgba(122,127,154,0.6); }
        .form-control.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 4px rgba(239,68,68,0.1); }
        .toggle-password {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer; font-size: 15px; transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--text); }
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .remember-check { display: flex; align-items: center; gap: 8px; }
        .remember-check input[type=checkbox] { accent-color: var(--accent); width: 16px; height: 16px; border-radius: 4px; }
        .remember-check label { font-size: 13px; color: var(--muted); cursor: pointer; }

        /* ── Sign in button ───────────────────────────────────────────── */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--accent) 0%, #7c5cfc 100%);
            border: none; border-radius: 12px;
            color: #fff; font-size: 14.5px; font-weight: 700; font-family: inherit;
            padding: 14px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.25s ease;
            box-shadow: 0 4px 24px rgba(108,99,255,0.3);
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 36px rgba(108,99,255,0.45); }
        .btn-login:active { transform: translateY(0) scale(0.99); }
        .btn-login::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── Alerts ──────────────────────────────────────────────────── */
        .alert-box {
            border-radius: 12px; padding: 12px 16px; font-size: 13px;
            margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .alert-error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; }
        .alert-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; }

        /* ── Divider ──────────────────────────────────────────────────── */
        .divider-row { display: flex; align-items: center; gap: 16px; margin: 28px 0; }
        .divider-row::before, .divider-row::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider-row span { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: rgba(122,127,154,0.5); }

        /* ── Demo accounts ────────────────────────────────────────────── */
        .demo-btn {
            width: 100%; background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06); border-radius: 10px;
            color: var(--text); font-size: 13px; font-weight: 600; font-family: inherit;
            padding: 11px 14px; cursor: pointer; margin-bottom: 8px;
            display: flex; align-items: center; gap: 10px;
            transition: all 0.2s ease; text-align: left;
        }
        .demo-btn:last-child { margin-bottom: 0; }
        .demo-btn:hover { background: var(--accent-soft); border-color: rgba(108,99,255,0.3); transform: translateX(4px); }
        .demo-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 8px currentColor; }
        .demo-meta { margin-left: auto; font-size: 11px; color: var(--muted); font-weight: 400; font-family: 'JetBrains Mono', monospace; }

        /* ── Responsive ───────────────────────────────────────────────── */
        @media (max-width: 900px) {
            body { overflow: auto; }
            .left-panel { display: none; }
            .right-panel { width: 100%; border: none; min-height: 100vh; }
            .login-box .logo-mobile { display: flex; }
        }
        @media (max-width: 480px) {
            .right-panel { padding: 28px 20px; }
            .login-box h2 { font-size: 24px; }
            .demo-btn { font-size: 12px; padding: 10px 12px; }
            .demo-meta { display: none; }
            .lang-switch { top: 12px; right: 12px; }
            .lang-btn { padding: 7px 10px; font-size: 12px; }
            .lang-dropdown { width: 200px; max-height: 300px; }
        }
    </style>
</head>
<body>

<!-- ── Language Switcher ───────────────────────────────────────────── -->
<div class="lang-switch" id="langSwitch">
    <button type="button" class="lang-btn" onclick="toggleLangDropdown()">
        <i class="bi bi-globe2"></i>
        <span id="currentLangLabel">English</span>
        <i class="bi bi-chevron-down lang-arrow"></i>
    </button>
    <div class="lang-dropdown" id="langDropdown"></div>
</div>

<!-- ── Left Panel ──────────────────────────────────────────────────── -->
<div class="left-panel">
    <div class="grid-dots"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="float-cards">
        <div class="float-card">
            <div class="fc-icon" style="background:rgba(34,197,94,0.15);color:#22c55e"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="fc-info">
                <div class="fc-val" style="color:#22c55e">+24.5%</div>
                <div class="fc-lbl" data-i18n="fc_growth">Revenue Growth</div>
            </div>
        </div>
        <div class="float-card">
            <div class="fc-icon" style="background:rgba(108,99,255,0.15);color:#6c63ff"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="fc-info">
                <div class="fc-val">1,284</div>
                <div class="fc-lbl" data-i18n="fc_sales">Total Sales</div>
            </div>
        </div>
        <div class="float-card">
            <div class="fc-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="bi bi-box-seam-fill"></i></div>
            <div class="fc-info">
                <div class="fc-val">326</div>
                <div class="fc-lbl" data-i18n="fc_products">Products Tracked</div>
            </div>
        </div>
    </div>

    <div class="brand">
        <div class="brand-icon">S</div>
        <div class="brand-name">StoreHub</div>
    </div>

    <div class="hero-text">
        <h1><span data-i18n="hero_line1">Run Your Store</span><br><span class="gradient" data-i18n="hero_line2">Like a Pro</span></h1>
        <p data-i18n="hero_desc">Track inventory, manage sales, monitor revenue, and grow your business — all from one powerful dashboard.</p>
    </div>

    <div class="features">
        <div class="feature-pill"><i class="bi bi-shield-lock-fill" style="color:var(--accent)"></i> <span data-i18n="feat_access">Role-Based Access</span></div>
        <div class="feature-pill"><i class="bi bi-bar-chart-fill" style="color:var(--success)"></i> <span data-i18n="feat_analytics">Real-Time Analytics</span></div>
        <div class="feature-pill"><i class="bi bi-box-seam-fill" style="color:var(--info)"></i> <span data-i18n="feat_inventory">Inventory Tracking</span></div>
        <div class="feature-pill"><i class="bi bi-cash-stack" style="color:var(--warning)"></i> <span data-i18n="feat_reports">Financial Reports</span></div>
    </div>
</div>

<!-- ── Right Panel ─────────────────────────────────────────────────── -->
<div class="right-panel">
    <div class="login-box">
        <div class="logo-mobile">
            <div class="brand-icon">S</div>
            <div class="brand-name">StoreHub</div>
        </div>

        <h2 data-i18n="welcome">Welcome back</h2>
        <p class="subtitle" data-i18n="subtitle">Sign in to your StoreHub account</p>

        @if($errors->any())
        <div class="alert-box alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('success'))
        <div class="alert-box alert-success">
            <i class="bi bi-check-circle-fill"></i> <span data-i18n="{{ session('flash_key') ?: '' }}">{{ session('success') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" data-i18n="label_email">Email Address</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" data-i18n="label_password">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" id="passwordField" class="form-control" data-i18n-placeholder="placeholder_pwd" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" onclick="togglePwd()" tabindex="-1">
                        <i class="bi bi-eye-fill" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <div class="remember-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" data-i18n="remember">Keep me signed in</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-arrow-right-short" style="font-size:18px"></i> <span data-i18n="btn_login">Sign In</span>
            </button>
        </form>

        <div class="divider-row"><span data-i18n="demo_section">Demo Accounts</span></div>

        <div class="demo-accounts">
            <button type="button" class="demo-btn" onclick="fillCreds('admin@store.com','Admin@1234')">
                <div class="demo-dot" style="color:#ef4444;background:#ef4444"></div>
                <span data-i18n="role_admin">Super Admin</span>
                <span class="demo-meta">admin@store.com</span>
            </button>
            <button type="button" class="demo-btn" onclick="fillCreds('owner@store.com','Owner@1234')">
                <div class="demo-dot" style="color:#f59e0b;background:#f59e0b"></div>
                <span data-i18n="role_owner">Shop Owner</span>
                <span class="demo-meta">owner@store.com</span>
            </button>
            <button type="button" class="demo-btn" onclick="fillCreds('seller1@store.com','Seller@1234')">
                <div class="demo-dot" style="color:#38bdf8;background:#38bdf8"></div>
                <span data-i18n="role_seller">Seller</span>
                <span class="demo-meta">seller1@store.com</span>
            </button>
        </div>
    </div>
</div>

<script src="/js/i18n.js"></script>
<script>
// ── Build Language Dropdown (using shared i18n.js) ──────────────
var loginDropdown = document.getElementById('langDropdown');
LANGUAGES.forEach(function(lang) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'lang-option';
    btn.setAttribute('data-code', lang.code);
    btn.onclick = function() { loginSetLang(lang.code); };
    var flagStr = '';
    try { flagStr = eval('"' + lang.flag + '"'); } catch(e) { flagStr = ''; }
    btn.innerHTML = '<span class="flag">' + flagStr + '</span><span class="lang-name">' + lang.name + '</span><i class="bi bi-check2 lang-check"></i>';
    loginDropdown.appendChild(btn);
});

function loginSetLang(lang) {
    localStorage.setItem('storehub_lang', lang);
    document.documentElement.lang = lang;
    var info = LANGUAGES.find(function(l){ return l.code === lang; });
    document.documentElement.dir = (info && info.rtl) ? 'rtl' : 'ltr';
    applyTranslations();
    loginUpdateUI();
    closeLangDropdown();
}

function loginUpdateUI() {
    var lang = localStorage.getItem('storehub_lang') || 'en';
    var t = T[lang] || T.en;
    document.getElementById('currentLangLabel').textContent = t.lang_label || lang;
    document.querySelectorAll('.lang-option').forEach(function(opt) {
        var code = opt.getAttribute('data-code');
        opt.classList.toggle('active', code === lang);
    });
}

function toggleLangDropdown() {
    document.getElementById('langSwitch').classList.toggle('open');
    var dd = document.getElementById('langDropdown');
    var active = dd.querySelector('.lang-option.active');
    if (active) active.scrollIntoView({ block: 'center' });
}

function closeLangDropdown() {
    document.getElementById('langSwitch').classList.remove('open');
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('langSwitch').contains(e.target)) closeLangDropdown();
});

// ── Auth Helpers ─────────────────────────────────────────────────
function fillCreds(email, password) {
    document.querySelector('input[name=email]').value = email;
    document.getElementById('passwordField').value = password;
    document.querySelector('input[name=email]').focus();
}

function togglePwd() {
    var f = document.getElementById('passwordField');
    var i = document.getElementById('eyeIcon');
    if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash-fill'; }
    else { f.type = 'password'; i.className = 'bi bi-eye-fill'; }
}

// ── Init ─────────────────────────────────────────────────────────
loginUpdateUI();
</script>
</body>
</html>
