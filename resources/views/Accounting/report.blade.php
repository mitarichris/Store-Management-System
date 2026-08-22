@extends('layouts.app')
@section('title', 'P&L Report')
@section('breadcrumb', 'Accounting / <span>P&L Report</span>')

@push('styles')
<style>
canvas { max-height:260px; }
.profit { color:var(--success); font-weight:700; }
.loss   { color:var(--danger);  font-weight:700; }
.annual-card {
    border-radius:var(--radius); padding:24px;
    background:var(--card-bg); border:1px solid var(--card-border);
    margin-bottom:24px;
}
@media(max-width:768px){
    .annual-card { padding:16px 12px; }
    .annual-card .row > div { margin-bottom:16px; }
    .annual-card .row > div:last-child { margin-bottom:0; }
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-earmark-bar-graph-fill me-2" style="color:var(--accent)"></i><span data-i18n="pnl_title">Profit &amp; Loss Report</span></h1>
    <div class="page-actions">
        <a href="{{ route('accounting.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span>
        </a>
    </div>
</div>

{{-- Year selector --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label" data-i18n="hist_year">Year</label>
                <select name="year" class="form-select" style="width:140px" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" data-i18n="hist_view">View</button>
        </form>
    </div>
</div>

{{-- Annual Summary --}}
<div class="annual-card">
    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px"><span data-i18n="acct_period_stock">Total Inventory Cost</span></div>
            <div style="font-size:26px;font-weight:800;color:var(--danger)">{{ number_format($annual['inventory']) }}</div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px"><span data-i18n="acct_period_profit">Total Profit Earned</span></div>
            <div style="font-size:26px;font-weight:800;color:var(--success)">{{ number_format($annual['revenue']) }}</div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px"><span data-i18n="acct_net_result">Net</span> <span data-i18n="{{ $annual['is_profit'] ? 'acct_profit' : 'acct_loss' }}">{{ $annual['is_profit'] ? 'Profit' : 'Loss' }}</span></div>
            <div style="font-size:26px;font-weight:800;color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
                {{ $annual['is_profit'] ? '+' : '-' }}{{ number_format(abs($annual['profit'])) }}
            </div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px"><span data-i18n="pnl_profit_margin">Profit Margin</span></div>
            <div style="font-size:26px;font-weight:800;color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
                @if($annual['inventory'] > 0)
                    {{ number_format(($annual['profit'] / $annual['inventory']) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div style="font-size:12px;color:var(--text-muted)"><span data-i18n="pnl_on_cost">On Cost</span></div>
        </div>
    </div>
    <div class="text-center mt-3">
        <span class="badge px-3 py-2" style="font-size:14px;background:{{ $annual['is_profit'] ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }};color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
            <span data-i18n="{{ $annual['is_profit'] ? 'pnl_profitable_year' : 'pnl_loss_year' }}">{{ $annual['is_profit'] ? '▲ PROFITABLE YEAR' : '▼ LOSS YEAR' }}</span> — {{ $year }}
        </span>
    </div>
</div>

{{-- Chart --}}
<div class="card mb-4">
    <div class="card-header"><span data-i18n="pnl_monthly_overview">Monthly Profit Overview</span> ({{ $year }})</div>
    <div class="card-body"><canvas id="plChart"></canvas></div>
</div>

{{-- Monthly Breakdown --}}
<div class="card">
    <div class="card-header"><span data-i18n="pnl_monthly_breakdown">Monthly Breakdown</span></div>
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th data-i18n="pnl_month">Month</th>
                    <th data-i18n="pnl_inventory_cost">Inventory Cost (RWF)</th>
                    <th data-i18n="pnl_profit_earned">Profit Earned (RWF)</th>
                    <th data-i18n="users_col_status">Status</th>
                    <th data-i18n="pnl_margin">Margin</th>
                </tr>
            </thead>
            <tbody>
            @foreach($monthly as $m)
            <tr>
                <td style="font-weight:600">{{ $m['month'] }}</td>
                <td style="color:var(--danger)">{{ number_format($m['inventory']) }}</td>
                <td class="{{ $m['is_profit'] ? 'profit' : ($m['revenue'] == 0 ? '' : 'loss') }}">
                    {{ $m['revenue'] > 0 ? '+' : '' }}{{ number_format($m['revenue']) }}
                </td>
                <td>
                    @if($m['revenue'] == 0 && $m['inventory'] == 0)
                        <span class="badge bg-secondary" data-i18n="pnl_no_activity">No Activity</span>
                    @elseif($m['revenue'] > 0)
                        <span class="badge bg-success" data-i18n="acct_profit">Profit</span>
                    @else
                        <span class="badge bg-secondary" data-i18n="pnl_no_sales">No Sales</span>
                    @endif
                </td>
                <td style="font-size:13px;color:var(--text-secondary)">
                    @if($m['inventory'] > 0 && $m['revenue'] > 0)
                        {{ number_format(($m['revenue'] / $m['inventory']) * 100, 1) }}%
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:rgba(255,255,255,0.03)">
                    <td style="font-weight:700"><span data-i18n="pnl_total">TOTAL</span></td>
                    <td style="color:var(--danger);font-weight:700">{{ number_format($annual['inventory']) }}</td>
                    <td class="{{ $annual['is_profit'] ? 'profit' : 'loss' }}" style="font-size:15px">
                        {{ $annual['is_profit'] ? '+' : '' }}{{ number_format($annual['profit']) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $annual['is_profit'] ? 'success' : 'danger' }}" style="font-size:12px">
                            <span data-i18n="{{ $annual['is_profit'] ? 'acct_profit' : 'acct_loss' }}">{{ $annual['is_profit'] ? 'Profit' : 'Loss' }}</span>
                        </span>
                    </td>
                    <td style="font-weight:700;color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
                        @if($annual['inventory'] > 0)
                            {{ number_format(($annual['profit'] / $annual['inventory']) * 100, 1) }}%
                        @else —
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthly = @json($monthly);
new Chart(document.getElementById('plChart'), {
    type:'bar',
    data:{
        labels: monthly.map(m=>m.month),
        datasets:[
            {label:'Inventory Cost', data:monthly.map(m=>m.inventory), backgroundColor:'rgba(239,68,68,0.6)', borderColor:'#ef4444', borderWidth:1, borderRadius:4},
            {label:'Profit Earned',  data:monthly.map(m=>m.revenue),  backgroundColor:'rgba(34,197,94,0.6)',  borderColor:'#22c55e', borderWidth:1, borderRadius:4},
            {label:'Net Profit',     data:monthly.map(m=>m.profit),   type:'line', borderColor:'#6c63ff', backgroundColor:'rgba(108,99,255,0.1)', borderWidth:2, pointRadius:5, fill:true, tension:0.4,
             pointBackgroundColor:monthly.map(m=>m.is_profit?'#22c55e':'#ef4444')}
        ]
    },
    options:{
        responsive:true, maintainAspectRatio:true,
        plugins:{legend:{labels:{color:'#8b90a7',font:{size:11}}}},
        scales:{
            x:{ticks:{color:'#8b90a7',font:{size:11}},grid:{color:'rgba(255,255,255,0.05)'}},
            y:{ticks:{color:'#8b90a7',font:{size:11}},grid:{color:'rgba(255,255,255,0.05)'}}
        }
    }
});
</script>
@endpush
