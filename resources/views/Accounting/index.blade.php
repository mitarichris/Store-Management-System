@extends('layouts.app')
@section('title', 'Accounting')
@section('breadcrumb', '<span>Accounting</span>')

@push('styles')
<style>
.acc-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
@media(max-width:900px){ .acc-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:500px){ .acc-grid{grid-template-columns:1fr;} }
.acc-card {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:var(--radius); padding:22px 20px;
    display:flex; flex-direction:column; gap:6px;
    transition:transform 0.2s;
}
.acc-card:hover { transform:translateY(-2px); }
.ac-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; }
.ac-value { font-size:28px; font-weight:800; line-height:1.15; }
.ac-sub   { font-size:12px; color:var(--text-muted); }
.acc-card.capital   { border-top:3px solid #38bdf8; }
.acc-card.inventory { border-top:3px solid #ef4444; }
.acc-card.revenue   { border-top:3px solid #22c55e; }
.acc-card.balance   { border-top:3px solid var(--accent); }
.tx-credit { color:var(--success); font-weight:700; }
.tx-debit  { color:var(--danger);  font-weight:700; }
.modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.65); z-index:2000;
    align-items:center; justify-content:center;
}
.modal-overlay.open { display:flex; }
.modal-box {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:var(--radius); padding:28px; width:100%; max-width:460px; margin:20px;
}
.modal-box h3 { font-size:16px; font-weight:700; margin-bottom:4px; }
.modal-sub { font-size:12px; color:var(--text-muted); margin-bottom:18px; line-height:1.6; }
.auto-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25);
    border-radius:8px; padding:10px 16px; font-size:13px;
    color:var(--success); margin-bottom:20px; width:100%;
}
canvas { max-height:220px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-calculator-fill me-2" style="color:var(--accent)"></i><span data-i18n="acct_title">Accounting</span></h1>
        <p style="color:var(--text-secondary);font-size:13px;margin:4px 0 0">
            <span data-i18n="acct_overview">Financial overview for</span> <strong>{{ auth()->user()->name }}</strong>
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('accounting.report') }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-bar-graph me-1"></i><span data-i18n="acct_pnl_report">P&L Report</span>
        </a>
        <button class="btn btn-outline-danger" onclick="openModal('inventoryModal')">
            <i class="bi bi-box-seam me-1"></i><span data-i18n="acct_record_purchase">Record Stock Purchase</span>
        </button>
        <button class="btn btn-primary" onclick="openModal('balanceModal')">
            <i class="bi bi-wallet-fill me-1"></i><span data-i18n="acct_set_capital">Set Capital</span>
        </button>
    </div>
</div>

<div class="auto-badge">
    <i class="bi bi-lightning-charge-fill"></i>
    <span data-i18n="acct_auto_badge">Revenue and balance update <strong>automatically</strong> every time a sale is completed — no manual entry needed.</span>
</div>

{{-- ── 4 Cards ──────────────────────────────────────────────────── --}}
<div class="acc-grid">

    <div class="acc-card capital">
        <div class="ac-label" style="color:#38bdf8">
            <i class="bi bi-wallet2 me-1"></i><span data-i18n="acct_opening_capital">Opening Capital</span>
        </div>
        <div class="ac-value">{{ number_format($account->opening_balance) }}</div>
        <div class="ac-sub"><span data-i18n="acct_capital_desc">RWF — starting cash (fixed)</span></div>
        <button class="btn btn-sm btn-outline-secondary mt-2" style="font-size:11px" onclick="openModal('balanceModal')">
            <i class="bi bi-pencil me-1"></i><span data-i18n="edit">Edit</span>
        </button>
    </div>

    <div class="acc-card inventory">
        <div class="ac-label" style="color:#ef4444">
            <i class="bi bi-box-seam-fill me-1"></i><span data-i18n="acct_inventory_label">Inventory</span>
        </div>
        <div class="ac-value" style="color:var(--danger)">
            {{ number_format($account->totalInventoryCost()) }}
        </div>
        <div class="ac-sub"><span data-i18n="acct_inventory_desc">RWF — total stock purchased</span></div>
        <button class="btn btn-sm btn-outline-danger mt-2" style="font-size:11px" onclick="openModal('inventoryModal')">
            <i class="bi bi-plus me-1"></i><span data-i18n="acct_record">Record</span>
        </button>
    </div>

    <div class="acc-card revenue">
        <div class="ac-label" style="color:#22c55e">
            <i class="bi bi-graph-up-arrow me-1"></i><span data-i18n="acct_revenue_profit">Revenue (Profit)</span>
        </div>
        <div class="ac-value" style="color:var(--success)">
            {{ number_format($account->totalRevenue()) }}
        </div>
        <div class="ac-sub"><span data-i18n="acct_revenue_desc">RWF — profit from sales (auto)</span></div>
        <a href="{{ route('sales.create') }}" class="btn btn-sm btn-outline-success mt-2" style="font-size:11px">
            <i class="bi bi-plus-circle me-1"></i><span data-i18n="acct_new_sale">New Sale</span>
        </a>
    </div>

    {{-- Balance = Capital + Revenue (profit) --}}
    @php
        $balance = $account->calculatedBalance();
        $isUp    = $balance >= (float)$account->opening_balance;
    @endphp
    <div class="acc-card balance" style="border-top-color:{{ $isUp ? '#22c55e' : '#f59e0b' }}">
        <div class="ac-label" style="color:var(--accent)">
            <i class="bi bi-cash-coin me-1"></i><span data-i18n="acct_current_balance">Current Balance</span>
        </div>
        <div class="ac-value" style="color:{{ $isUp ? 'var(--success)' : 'var(--warning)' }}">
            {{ number_format($balance) }}
        </div>
        <div class="ac-sub"><span data-i18n="acct_balance_desc">RWF — Capital + Revenue</span></div>
        @if($account->totalRevenue() > 0)
        <div style="font-size:12px;font-weight:700;color:var(--success);margin-top:4px">
            <span data-i18n="acct_profit">▲ Profit:</span> {{ number_format($account->totalRevenue()) }} RWF
        </div>
        @elseif($account->totalRevenue() == 0)
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px"><span data-i18n="acct_no_sales">No sales yet</span></div>
        @endif
    </div>
</div>

{{-- Period filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="form-label"><span data-i18n="acct_filter_period">Filter Period</span></label>
                <select name="period" class="form-select" style="width:160px" onchange="this.form.submit()">
                    @foreach(['all'=>'All Time','today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year','custom'=>'Custom Range'] as $v=>$l)
                    <option value="{{ $v }}" {{ $period===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @if($period==='custom')
            <div><label class="form-label" data-i18n="acct_from">From</label><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div><label class="form-label" data-i18n="acct_to">To</label><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <button type="submit" class="btn btn-primary" data-i18n="acct_apply">Apply</button>
            @endif
            <a href="{{ route('accounting.index') }}" class="btn btn-outline-secondary" data-i18n="acct_reset">Reset</a>
        </form>
    </div>
</div>

@if($period !== 'all')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px"><span data-i18n="acct_period_stock">Period Stock Cost</span></div>
            <div style="font-size:22px;font-weight:800;color:var(--danger)">{{ number_format($periodInventory) }} RWF</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px"><span data-i18n="acct_period_profit">Period Profit Earned</span></div>
            <div style="font-size:22px;font-weight:800;color:var(--success)">{{ number_format($periodRevenue) }} RWF</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px"><span data-i18n="acct_net_result">Net Result</span></div>
            <div style="font-size:22px;font-weight:800;color:{{ $periodProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                {{ $periodProfit >= 0 ? '+' : '' }}{{ number_format($periodProfit) }} RWF
            </div>
        </div>
    </div>
</div>
@endif

{{-- Chart + actions --}}
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><span data-i18n="acct_monthly_overview">Monthly Overview</span> ({{ now()->year }})</div>
            <div class="card-body"><canvas id="accountChart"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><span data-i18n="acct_quick_actions">Quick Actions</span></div>
            <div class="card-body d-flex flex-column gap-3">
                <button class="btn btn-outline-info w-100" onclick="openModal('balanceModal')">
                    <i class="bi bi-wallet2 me-2"></i><span data-i18n="acct_set_capital_btn">Set Opening Capital</span>
                </button>
                <button class="btn btn-outline-danger w-100" onclick="openModal('inventoryModal')">
                    <i class="bi bi-box-seam me-2"></i><span data-i18n="acct_record_purchase">Record Stock Purchase</span>
                </button>
                <a href="{{ route('sales.create') }}" class="btn btn-outline-success w-100">
                    <i class="bi bi-plus-circle me-2"></i><span data-i18n="acct_new_sale_btn">New Sale (auto-records profit)</span>
                </a>
                <a href="{{ route('accounting.report') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i><span data-i18n="acct_pnl_report">P&L Report</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Transactions table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span data-i18n="acct_transaction_history">Transaction History</span>
        <span class="badge" style="background:var(--accent-soft);color:var(--accent)"><span data-i18n="acct_auto_recorded">Revenue auto-recorded from sales</span></span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th data-i18n="acct_col_date">Date</th><th data-i18n="acct_col_type">Type</th><th data-i18n="acct_col_desc">Description</th><th data-i18n="acct_col_amount">Amount (RWF)</th><th data-i18n="acct_col_balance">Balance After (RWF)</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td style="font-size:13px;color:var(--text-secondary)">{{ $tx->transaction_date->format('d M Y') }}</td>
                <td><span class="badge bg-{{ $tx->type_badge_color }}">{{ $tx->type_label }}</span></td>
                <td style="font-size:12.5px">{{ $tx->description }}</td>
                <td class="{{ $tx->isCredit() ? 'tx-credit' : 'tx-debit' }}">
                    {{ $tx->isCredit() ? '+' : '−' }}{{ number_format($tx->amount) }}
                </td>
                <td style="font-weight:700">{{ number_format($tx->balance_after) }}</td>
                <td>
                    <form method="POST" action="{{ route('accounting.destroy', $tx) }}"
                          onsubmit="return confirm('Delete this transaction?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:8px"></i>
                    <span data-i18n="acct_no_transactions">No transactions yet. Set your opening capital, then make sales!</span>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-body" style="border-top:1px solid var(--card-border)">{{ $transactions->links() }}</div>
    @endif
</div>

{{-- Opening Capital Modal --}}
<div class="modal-overlay" id="balanceModal">
    <div class="modal-box">
        <h3><i class="bi bi-wallet2 me-2"></i><span data-i18n="acct_set_capital_title">Set Opening Capital</span></h3>
        <div class="modal-sub"><span data-i18n="acct_capital_explain">The cash you had before buying stock. Fixed — never changes automatically.</span></div>
        <form method="POST" action="{{ route('accounting.opening-balance') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label"><span data-i18n="acct_capital_label">Opening Capital (RWF) *</span></label>
                <input type="number" name="opening_balance" class="form-control"
                    value="{{ $account->opening_balance }}" min="0" step="1" required placeholder="e.g. 100000">
            </div>
            <div class="mb-3">
                <label class="form-label" data-i18n="acct_date_label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ today()->toDateString() }}">
            </div>
            <div class="mb-4">
                <label class="form-label" data-i18n="acct_notes_optional">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2">{{ $account->notes }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" data-i18n="acct_save_capital">Save Capital</button>
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal('balanceModal')" data-i18n="acct_cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Stock Purchase Modal --}}
<div class="modal-overlay" id="inventoryModal">
    <div class="modal-box">
        <h3><i class="bi bi-box-seam me-2"></i><span data-i18n="acct_purchase_title">Record Stock Purchase</span></h3>
        <div class="modal-sub">
            <span data-i18n="acct_purchase_explain">Record how much you spent buying stock. This is tracked in Inventory only.</span><br>
            <span data-i18n="acct_purchase_example">Example: Buy stock at 40,000 RWF → Inventory = 40,000 RWF.</span>
        </div>
        <form method="POST" action="{{ route('accounting.inventory') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" data-i18n="acct_product_label">Product *</label>
                <select name="product_id" class="form-select" required onchange="fillCost(this)">
                    <option value="" data-i18n="acct_select_product">Select product…</option>
                    @foreach(\App\Models\Product::where('is_active', true)->get() as $p)
                    <option value="{{ $p->id }}" data-cost="{{ $p->cost_price }}">
                        {{ $p->name }} — Cost: {{ number_format($p->cost_price) }} RWF
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" data-i18n="acct_amount_paid">Amount Paid (RWF) *</label>
                <input type="number" name="cost_price" id="costInput" class="form-control"
                    min="0" step="1" required placeholder="e.g. 40000">
            </div>
            <div class="mb-3">
                <label class="form-label" data-i18n="acct_date_label">Date *</label>
                <input type="date" name="date" class="form-control" value="{{ today()->toDateString() }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label" data-i18n="acct_desc_label">Description (optional)</label>
                <input type="text" name="description" class="form-control" placeholder="e.g. Bought 10 units of Samsung A54">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger" data-i18n="acct_record_purchase_btn">Record Purchase</button>
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal('inventoryModal')" data-i18n="acct_cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const data = @json($chartData);
new Chart(document.getElementById('accountChart'), {
    type:'bar',
    data:{
        labels:data.map(d=>d.month),
        datasets:[
            {label:'Stock Purchased', data:data.map(d=>d.inventory), backgroundColor:'rgba(239,68,68,0.6)', borderColor:'#ef4444', borderWidth:1, borderRadius:4},
            {label:'Profit Earned',   data:data.map(d=>d.revenue),   backgroundColor:'rgba(34,197,94,0.6)',  borderColor:'#22c55e', borderWidth:1, borderRadius:4},
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
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m=>{
    m.addEventListener('click',e=>{if(e.target===m) m.classList.remove('open');});
});
function fillCost(sel) {
    const cost = sel.options[sel.selectedIndex].dataset.cost || '';
    document.getElementById('costInput').value = cost ? Math.round(cost) : '';
}
</script>
@endpush
