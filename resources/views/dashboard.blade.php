@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', '<span>Dashboard</span>')

@push('styles')
<style>
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .chart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px; }
    @media (max-width: 1024px) { .chart-grid { grid-template-columns: 1fr; } }
    .table-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 900px) { .table-grid { grid-template-columns: 1fr; } }
    canvas { max-height: 240px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin:4px 0 0">
            Welcome back, <strong>{{ auth()->user()->name }}</strong> &middot; {{ now()->format('l, d F Y') }}
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
            <div class="stat-label">My Total Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#22c55e"><i class="bi bi-cash-coin"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['my_revenue']) }} RWF</div>
            <div class="stat-label">My Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="bi bi-box-seam-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label">Available Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['low_stock']) }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        My Recent Sales
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Sale</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Sale #</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($stats['my_recent_sales'] as $sale)
            <tr>
                <td><span style="font-family:monospace;color:var(--accent)">{{ $sale->sale_number }}</span></td>
                <td>{{ $sale->customer_name ?? '—' }}</td>
                <td><strong>{{ number_format($sale->net_amount) }} RWF</strong></td>
                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$sale->payment_method)) }}</span></td>
                <td style="color:var(--text-secondary)">{{ $sale->created_at->diffForHumans() }}</td>
                <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted)">No sales yet. <a href="{{ route('sales.create') }}">Create your first sale →</a></td></tr>
            @endforelse
            </tbody>
        </table>
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
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    @endif
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="bi bi-box-seam-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label">Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_sales']) }}</div>
            <div class="stat-label">Total Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#22c55e"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_revenue']) }}</div>
            <div class="stat-label">Revenue (RWF)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15);color:#ef4444"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['low_stock']) }}</div>
            <div class="stat-label">Low Stock Alerts</div>
        </div>
    </div>
</div>

<!-- Charts row -->
<div class="chart-grid">
    <div class="card">
        <div class="card-header">Monthly Revenue</div>
        <div class="card-body"><canvas id="revenueChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header">Top Products</div>
        <div class="card-body">
            @foreach($stats['top_products'] as $p)
            <div class="d-flex align-items-center gap-2 mb-3">
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ number_format($p->total_sold) }} units sold</div>
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
            Recent Sales
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Sale #</th><th>Seller</th><th>Amount</th><th>Status</th></tr></thead>
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
    @if($user->isSuperAdmin())
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            Seller Performance
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Seller</th><th>Sales</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($stats['sellers'] as $seller)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $seller->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $seller->email }}</div>
                    </td>
                    <td><span class="badge" style="background:var(--accent-soft);color:var(--accent)">{{ $seller->sales_count }}</span></td>
                    <td><span class="badge bg-{{ $seller->is_active ? 'success' : 'secondary' }}">{{ $seller->is_active ? 'Active' : 'Inactive' }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
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
