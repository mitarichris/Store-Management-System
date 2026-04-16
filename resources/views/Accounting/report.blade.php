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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-earmark-bar-graph-fill me-2" style="color:var(--accent)"></i>Profit & Loss Report</h1>
    <div class="page-actions">
        <a href="{{ route('accounting.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

{{-- Year selector --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label">Year</label>
                <select name="year" class="form-select" style="width:140px" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View</button>
        </form>
    </div>
</div>

{{-- Annual Summary --}}
<div class="annual-card">
    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Total Inventory Cost</div>
            <div style="font-size:26px;font-weight:800;color:var(--danger)">{{ number_format($annual['inventory']) }}</div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Total Profit Earned</div>
            <div style="font-size:26px;font-weight:800;color:var(--success)">{{ number_format($annual['revenue']) }}</div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Net {{ $annual['is_profit'] ? 'Profit' : 'Loss' }}</div>
            <div style="font-size:26px;font-weight:800;color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
                {{ $annual['is_profit'] ? '+' : '-' }}{{ number_format(abs($annual['profit'])) }}
            </div>
            <div style="font-size:12px;color:var(--text-muted)">RWF</div>
        </div>
        <div class="col-md-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Profit Margin</div>
            <div style="font-size:26px;font-weight:800;color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
                @if($annual['inventory'] > 0)
                    {{ number_format(($annual['profit'] / $annual['inventory']) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div style="font-size:12px;color:var(--text-muted)">On Cost</div>
        </div>
    </div>
    <div class="text-center mt-3">
        <span class="badge px-3 py-2" style="font-size:14px;background:{{ $annual['is_profit'] ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }};color:{{ $annual['is_profit'] ? 'var(--success)' : 'var(--danger)' }}">
            {{ $annual['is_profit'] ? '▲ PROFITABLE YEAR' : '▼ LOSS YEAR' }} — {{ $year }}
        </span>
    </div>
</div>

{{-- Chart --}}
<div class="card mb-4">
    <div class="card-header">Monthly Profit Overview ({{ $year }})</div>
    <div class="card-body"><canvas id="plChart"></canvas></div>
</div>

{{-- Monthly Breakdown --}}
<div class="card">
    <div class="card-header">Monthly Breakdown</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Inventory Cost (RWF)</th>
                    <th>Profit Earned (RWF)</th>
                    <th>Status</th>
                    <th>Margin</th>
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
                        <span class="badge bg-secondary">No Activity</span>
                    @elseif($m['revenue'] > 0)
                        <span class="badge bg-success">Profit</span>
                    @else
                        <span class="badge bg-secondary">No Sales</span>
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
                    <td style="font-weight:700">TOTAL</td>
                    <td style="color:var(--danger);font-weight:700">{{ number_format($annual['inventory']) }}</td>
                    <td class="{{ $annual['is_profit'] ? 'profit' : 'loss' }}" style="font-size:15px">
                        {{ $annual['is_profit'] ? '+' : '' }}{{ number_format($annual['profit']) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $annual['is_profit'] ? 'success' : 'danger' }}" style="font-size:12px">
                            {{ $annual['is_profit'] ? 'Profit' : 'Loss' }}
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
