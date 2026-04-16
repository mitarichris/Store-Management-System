<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StoreHub') — StoreHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 260px;
            --sidebar-collapsed-w: 70px;
            --topbar-h: 60px;
            --bg: #0f1117;
            --sidebar-bg: #13161f;
            --sidebar-border: rgba(255,255,255,0.06);
            --card-bg: #1a1d28;
            --card-border: rgba(255,255,255,0.07);
            --accent: #6c63ff;
            --accent-soft: rgba(108,99,255,0.15);
            --accent-hover: #5a52e0;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #38bdf8;
            --text-primary: #f0f2f8;
            --text-secondary: #8b90a7;
            --text-muted: #555b72;
            --radius: 12px;
            --radius-sm: 8px;
            --transition: 0.2s ease;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Sidebar ─────────────────────────────────────────────────────── */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column;
            transition: width var(--transition);
            z-index: 1000;
            overflow: hidden;
        }
        #sidebar.collapsed { width: var(--sidebar-collapsed-w); }

        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            min-height: var(--topbar-h);
            white-space: nowrap;
        }
        .sidebar-logo .logo-icon {
            width: 34px; height: 34px; flex-shrink: 0;
            background: var(--accent);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 800; color: #fff;
            letter-spacing: -1px;
        }
        .sidebar-logo .logo-text {
            font-size: 18px; font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        /* Role badge */
        .role-badge {
            margin: 12px 14px;
            padding: 10px 12px;
            background: var(--accent-soft);
            border: 1px solid rgba(108,99,255,0.25);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; gap: 10px;
            white-space: nowrap; overflow: hidden;
        }
        .role-badge .avatar {
            width: 32px; height: 32px; flex-shrink: 0;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
        }
        .role-badge .user-info { min-width: 0; }
        .role-badge .user-name { font-size: 12.5px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .role-badge .user-role { font-size: 10.5px; color: var(--accent); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Nav */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 8px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: 2px; }

        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: var(--text-muted);
            padding: 12px 20px 4px;
            white-space: nowrap; overflow: hidden;
        }
        #sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item { padding: 2px 10px; }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all var(--transition);
            white-space: nowrap; overflow: hidden;
            position: relative;
        }
        .nav-link i { font-size: 17px; flex-shrink: 0; width: 20px; text-align: center; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }
        .nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 8px; bottom: 8px;
            width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
            margin-left: -10px;
        }
        .nav-badge {
            margin-left: auto; flex-shrink: 0;
            background: var(--danger);
            color: #fff; font-size: 10px; font-weight: 700;
            padding: 1px 6px; border-radius: 10px; line-height: 1.6;
        }
        #sidebar.collapsed .nav-badge,
        #sidebar.collapsed .nav-link span { display: none; }

        /* Sidebar footer */
        .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 10px;
        }
        .sidebar-footer .nav-link { color: var(--danger); }
        .sidebar-footer .nav-link:hover { background: rgba(239,68,68,0.1); }

        /* ── Topbar ───────────────────────────────────────────────────────── */
        #topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: rgba(15,17,23,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; gap: 16px;
            padding: 0 24px;
            z-index: 999;
            transition: left var(--transition);
        }
        #sidebar.collapsed ~ #topbar,
        #sidebar.collapsed ~ #main-wrap #topbar { left: var(--sidebar-collapsed-w); }

        .topbar-toggle {
            width: 34px; height: 34px;
            background: none; border: 1px solid var(--card-border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 16px;
            transition: all var(--transition);
        }
        .topbar-toggle:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }

        .topbar-breadcrumb { font-size: 13.5px; color: var(--text-secondary); }
        .topbar-breadcrumb span { color: var(--text-primary); font-weight: 600; }

        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .topbar-btn {
            width: 34px; height: 34px;
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 15px;
            transition: all var(--transition); text-decoration: none;
        }
        .topbar-btn:hover { color: var(--text-primary); border-color: var(--accent); }

        /* ── Main content ─────────────────────────────────────────────────── */
        #main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            transition: margin-left var(--transition);
        }
        #sidebar.collapsed ~ #main-wrap { margin-left: var(--sidebar-collapsed-w); }

        .main-content { padding: 28px 28px; }

        /* ── Cards ────────────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            padding: 16px 20px;
            font-weight: 700; font-size: 14px;
        }
        .card-body { padding: 20px; }

        /* Stat cards */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            padding: 20px;
            display: flex; align-items: flex-start; gap: 16px;
            transition: transform var(--transition), border-color var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); border-color: var(--accent); }
        .stat-icon {
            width: 46px; height: 46px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .stat-info { min-width: 0; }
        .stat-value { font-size: 22px; font-weight: 800; line-height: 1.2; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; margin-top: 2px; }
        .stat-change { font-size: 11.5px; font-weight: 600; margin-top: 4px; }

        /* ── Tables ───────────────────────────────────────────────────────── */
        .table { color: var(--text-primary); font-size: 13.5px; }
        .table th {
            font-size: 11px; font-weight: 700; letter-spacing: 0.5px;
            text-transform: uppercase; color: var(--text-muted);
            border-bottom: 1px solid var(--card-border) !important;
            padding: 12px 16px;
        }
        .table td {
            border-bottom: 1px solid rgba(255,255,255,0.04) !important;
            padding: 12px 16px; vertical-align: middle;
        }
        .table tbody tr:hover td { background: rgba(255,255,255,0.03); }

        /* ── Forms ────────────────────────────────────────────────────────── */
        .form-control, .form-select {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 13.5px;
            padding: 9px 14px;
            transition: border-color var(--transition);
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.06);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.15);
            color: var(--text-primary);
            outline: none;
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-select option { background: #1a1d28; }
        .form-label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }

        /* ── Buttons ──────────────────────────────────────────────────────── */
        .btn { border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; padding: 8px 16px; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }

        /* ── Badges ───────────────────────────────────────────────────────── */
        .badge { font-size: 11px; font-weight: 600; padding: 4px 9px; border-radius: 6px; }

        /* ── Alerts ───────────────────────────────────────────────────────── */
        .alert { border-radius: var(--radius-sm); font-size: 13.5px; border: none; }
        .alert-success { background: rgba(34,197,94,0.12); color: #4ade80; }
        .alert-danger  { background: rgba(239,68,68,0.12); color: #f87171; }
        .alert-warning { background: rgba(245,158,11,0.12); color: #fbbf24; }
        .alert-info    { background: rgba(56,189,248,0.12); color: #38bdf8; }

        /* ── Page header ──────────────────────────────────────────────────── */
        .page-header {
            display: flex; align-items: center; gap: 16px;
            margin-bottom: 24px;
        }
        .page-header h1 { font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .page-header .page-actions { margin-left: auto; display: flex; gap: 8px; }

        /* ── Pagination ───────────────────────────────────────────────────── */
        .pagination .page-link {
            background: var(--card-bg); border-color: var(--card-border);
            color: var(--text-secondary); border-radius: var(--radius-sm) !important;
            font-size: 13px; margin: 0 2px;
        }
        .pagination .page-link:hover { background: var(--accent-soft); color: var(--accent); border-color: var(--accent); }
        .pagination .page-item.active .page-link { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Mobile ───────────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); width: var(--sidebar-w) !important; }
            #sidebar.mobile-open { transform: translateX(0); }
            #topbar { left: 0 !important; }
            #main-wrap { margin-left: 0 !important; }
            .main-content { padding: 16px; }
            .sidebar-overlay { display: block !important; }
        }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── Sidebar ────────────────────────────────────────────────────────────── -->
<nav id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">S</div>
        <span class="logo-text">StoreHub</span>
    </div>

    <!-- User badge -->
    <div class="role-badge">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">{{ auth()->user()->role_label }}</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <!-- Dashboard -->
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
            </a>
        </div>

        {{-- ── Management section ──────────────────────────────── --}}
        @if(auth()->user()->hasPermission('view_users'))
        <div class="nav-section-label">Management</div>
        <div class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i><span>Users</span>
            </a>
        </div>
        @endif

        {{-- ── Inventory section ──────────────────────────────── --}}
        @if(auth()->user()->hasPermission('view_categories') || auth()->user()->hasPermission('view_products'))
        <div class="nav-section-label">Inventory</div>
        @endif

        @if(auth()->user()->hasPermission('view_categories'))
        <div class="nav-item">
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tag-fill"></i><span>Categories</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasPermission('view_products'))
        @php $lowStock = \App\Models\Product::whereColumn('stock_quantity','<=','low_stock_threshold')->count(); @endphp
        <div class="nav-item">
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i>
                <span>Products</span>
                @if($lowStock > 0)<span class="nav-badge">{{ $lowStock }}</span>@endif
            </a>
        </div>
        @endif

        {{-- ── Sales section ──────────────────────────────── --}}
        @if(auth()->user()->hasPermission('view_sales'))
        <div class="nav-section-label">Sales</div>
        <div class="nav-item">
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i><span>All Sales</span>
            </a>
        </div>
        @if(auth()->user()->hasPermission('create_sales'))
        <div class="nav-item">
            <a href="{{ route('sales.create') }}" class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i><span>New Sale</span>
            </a>
        </div>
        @endif
        @endif
    </div>

    {{-- ── History ──────────────────────────────── --}}
<div class="nav-section-label">History</div>
<div class="nav-item">
    <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i><span>Sales History</span>
    </a>
</div>

    {{-- ── Accounting ──────────────────────────────── --}}
<div class="nav-section-label">Accounting</div>
<div class="nav-item">
    <a href="{{ route('accounting.index') }}" class="nav-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}">
        <i class="bi bi-calculator-fill"></i><span>Accounting</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('accounting.report') }}" class="nav-link {{ request()->routeIs('accounting.report') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-bar-graph"></i><span>P&L Report</span>
    </a>
</div>

    <!-- Logout -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start" style="color: var(--danger)">
                <i class="bi bi-box-arrow-left"></i><span>Logout</span>
            </button>
        </form>
    </div>
</nav>

<!-- ── Main wrapper ────────────────────────────────────────────────────────── -->
<div id="main-wrap">
    <!-- Topbar -->
    <div id="topbar">
        <button class="topbar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-breadcrumb">
            @yield('breadcrumb', '<span>Dashboard</span>')
        </div>
        <div class="topbar-right">
            <a href="{{ route('dashboard') }}" class="topbar-btn" title="Dashboard">
                <i class="bi bi-house-fill"></i>
            </a>
            <span class="topbar-btn" style="font-size:11px;font-weight:700;width:auto;padding:0 12px;color:var(--text-secondary)">
                {{ now()->format('d M Y') }}
            </span>
        </div>
    </div>

    <!-- Page content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const mainWrap = document.getElementById('main-wrap');
    const topbar = document.getElementById('topbar');
    let collapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    function applySidebarState() {
        if (window.innerWidth <= 768) return;
        if (collapsed) sidebar.classList.add('collapsed');
        else sidebar.classList.remove('collapsed');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('mobile-open');
            document.getElementById('sidebarOverlay').style.display =
                sidebar.classList.contains('mobile-open') ? 'block' : 'none';
        } else {
            collapsed = !collapsed;
            localStorage.setItem('sidebarCollapsed', collapsed);
            applySidebarState();
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        document.getElementById('sidebarOverlay').style.display = 'none';
    }

    applySidebarState();
    window.addEventListener('resize', applySidebarState);
</script>
@stack('scripts')
</body>
</html>
