@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', '<span>Dashboard</span>')

@push('styles')
<style>
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .chart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px; }
    @media (max-width: 1024px) { .chart-grid { grid-template-columns: 1fr; } }
    .table-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 900px) { .table-grid { grid-template-columns: 1fr; } }
    canvas { max-height: 240px; }
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 12px; gap: 10px; flex-direction: column; align-items: flex-start; }
        .stat-icon { width: 34px; height: 34px; font-size: 15px; }
        .stat-value { font-size: 16px; }
        .stat-label { font-size: 11px; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
        .stat-card { padding: 10px; gap: 8px; }
        .stat-value { font-size: 14px; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 data-i18n="dash_title">Dashboard</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin:4px 0 0">
            <span data-i18n="dash_welcome">Welcome back</span>, <strong>{{ auth()->user()->name }}</strong> &middot; {{ now()->format('l, d F Y') }}
        </p>
    </div>
</div>

@php $user = auth()->user(); @endphp

{{-- ── Seller dashboard ─────────────────────────────────────────── --}}
@if($user->isSeller())
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(108,99,255,0.15);color:#6c63ff"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['my_sales_count']) }}</div>
            <div class="stat-label" data-i18n="dash_my_total_sales">My Total Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#22c55e"><i class="bi bi-cash-coin"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['my_revenue']) }} RWF</div>
            <div class="stat-label" data-i18n="dash_my_revenue">My Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="bi bi-box-seam-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label" data-i18n="dash_available_products">Available Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['low_stock']) }}</div>
            <div class="stat-label" data-i18n="dash_low_stock_items">Low Stock Items</div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span data-i18n="dash_my_recent_sales">My Recent Sales</span>
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i><span data-i18n="dash_new_sale">New Sale</span></a>
    </div>
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead><tr><th data-i18n="hist_col_num">Sale #</th><th data-i18n="sales_col_customer">Customer</th><th data-i18n="sales_amount">Amount</th><th data-i18n="sales_payment">Payment</th><th data-i18n="sales_date">Date</th><th></th></tr></thead>
            <tbody>
            @forelse($stats['my_recent_sales'] as $sale)
            <tr>
                <td><span style="font-family:monospace;color:var(--accent)">{{ $sale->sale_number }}</span></td>
                <td>{{ $sale->customer_name ?? '—' }}</td>
                <td><strong>{{ number_format($sale->net_amount) }} RWF</strong></td>
                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$sale->payment_method)) }}</span></td>
                <td style="color:var(--text-secondary)">{{ $sale->created_at->diffForHumans() }}</td>
                <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary"><span data-i18n="dash_view">View</span></a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted)"><span data-i18n="dash_no_sales">No sales yet.</span> <a href="{{ route('sales.create') }}"><span data-i18n="dash_create_first">Create your first sale →</span></a></td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

{{-- ── Admin / Owner dashboard ─────────────────────────────────── --}}
@else
<div class="stat-grid">
    @if($user->isSuperAdmin())
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(108,99,255,0.15);color:#6c63ff"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label" data-i18n="dash_total_users">Total Users</div>
        </div>
    </div>
    @endif
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="bi bi-box-seam-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label" data-i18n="dash_products_stat">Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_sales']) }}</div>
            <div class="stat-label" data-i18n="dash_total_sales_stat">Total Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#22c55e"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_revenue']) }}</div>
            <div class="stat-label" data-i18n="dash_revenue_rwf">Revenue (RWF)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15);color:#ef4444"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['low_stock']) }}</div>
            <div class="stat-label" data-i18n="dash_low_stock_alerts">Low Stock Alerts</div>
        </div>
    </div>
</div>

<!-- Charts row -->
<div class="chart-grid">
    <div class="card">
        <div class="card-header" data-i18n="dash_monthly_revenue">Monthly Revenue</div>
        <div class="card-body"><canvas id="revenueChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header" data-i18n="dash_top_products">Top Products</div>
        <div class="card-body">
            @foreach($stats['top_products'] as $p)
            <div class="d-flex align-items-center gap-2 mb-3">
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ number_format($p->total_sold) }} <span data-i18n="dash_units_sold">units sold</span></div>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--success)">{{ number_format($p->revenue) }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Tables row -->
<div class="table-grid">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span data-i18n="dash_recent_sales">Recent Sales</span>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary"><span data-i18n="dash_view_all">View All</span></a>
        </div>
        <div class="card-body p-0">
            <div class="table-scroll">
            <table class="table mb-0">
                <thead><tr><th data-i18n="hist_col_num">Sale #</th><th data-i18n="dash_seller">Seller</th><th data-i18n="sales_amount">Amount</th><th data-i18n="status">Status</th></tr></thead>
                <tbody>
                @foreach($stats['recent_sales'] as $sale)
                <tr>
                    <td><a href="{{ route('sales.show',$sale) }}" style="color:var(--accent);font-family:monospace">{{ $sale->sale_number }}</a></td>
                    <td style="font-size:12px">{{ $sale->seller->name }}</td>
                    <td style="font-weight:700">{{ number_format($sale->net_amount) }}</td>
                    <td><span class="badge bg-{{ $sale->status_badge_color }}">{{ ucfirst($sale->status) }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @if($user->isSuperAdmin())
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span data-i18n="dash_seller_performance">Seller Performance</span>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary"><span data-i18n="dash_manage">Manage</span></a>
        </div>
        <div class="card-body p-0">
            <div class="table-scroll">
            <table class="table mb-0">
                <thead><tr><th data-i18n="dash_seller">Seller</th><th data-i18n="dash_seller_sales">Sales</th><th data-i18n="status">Status</th></tr></thead>
                <tbody>
                @foreach($stats['sellers'] as $seller)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $seller->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $seller->email }}</div>
                    </td>
                    <td><span class="badge" style="background:var(--accent-soft);color:var(--accent)">{{ $seller->sales_count }}</span></td>
                    <td><span class="badge bg-{{ $seller->is_active ? 'success' : 'secondary' }}"><span data-i18n="{{ $seller->is_active ? 'active' : 'inactive' }}">{{ $seller->is_active ? 'Active' : 'Inactive' }}</span></span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

@endsection

@push('scripts')
@if(!$user->isSeller())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthly = @json($stats['monthly_revenue'] ?? []);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: monthly.map(r => r.label),
        datasets: [{
            label: 'Revenue (RWF)',
            data: monthly.map(r => r.total),
            backgroundColor: 'rgba(108,99,255,0.5)',
            borderColor: '#6c63ff',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#8b90a7', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { ticks: { color: '#8b90a7', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.05)' } }
        }
    }
});
</script>
@endif
@endpush
