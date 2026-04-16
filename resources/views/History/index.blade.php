@extends('layouts.app')
@section('title', 'Sales History')
@section('breadcrumb', '<span>Sales History</span>')

@push('styles')
<style>
.view-tabs { display:flex; gap:6px; margin-bottom:20px; }
.view-tab {
    padding:8px 20px; border-radius:8px; font-size:13px; font-weight:600;
    border:1px solid var(--card-border); background:var(--card-bg);
    color:var(--text-secondary); cursor:pointer; text-decoration:none;
    transition:all 0.2s;
}
.view-tab.active, .view-tab:hover {
    background:var(--accent-soft); color:var(--accent); border-color:var(--accent);
}

/* Calendar */
.calendar-wrap { background:var(--card-bg); border:1px solid var(--card-border); border-radius:var(--radius); padding:20px; margin-bottom:20px; }
.cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.cal-header h3 { font-size:15px; font-weight:700; }
.cal-nav { display:flex; gap:6px; }
.cal-nav a { padding:5px 12px; border-radius:6px; border:1px solid var(--card-border); background:var(--card-bg); color:var(--text-secondary); font-size:12px; text-decoration:none; transition:all 0.2s; }
.cal-nav a:hover { background:var(--accent-soft); color:var(--accent); border-color:var(--accent); }
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
.cal-day-label { text-align:center; font-size:11px; font-weight:700; color:var(--text-muted); padding:4px 0; }
.cal-day {
    aspect-ratio:1; border-radius:6px; display:flex; flex-direction:column;
    align-items:center; justify-content:center; font-size:12px;
    border:1px solid transparent; cursor:pointer; transition:all 0.15s;
    text-decoration:none; color:var(--text-secondary);
    background:rgba(255,255,255,0.02);
}
.cal-day:hover { border-color:var(--accent); color:var(--accent); }
.cal-day.has-sales { background:rgba(108,99,255,0.12); border-color:rgba(108,99,255,0.3); color:var(--accent); }
.cal-day.selected { background:var(--accent); color:#fff !important; border-color:var(--accent); }
.cal-day.today { border-color:var(--warning) !important; }
.cal-day.empty { background:transparent; border:none; cursor:default; }
.cal-day .day-num { font-size:12px; font-weight:600; line-height:1; }
.cal-day .day-count { font-size:9px; margin-top:2px; opacity:0.8; }

/* Summary cards */
.summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:20px; }
.sum-card { background:var(--card-bg); border:1px solid var(--card-border); border-radius:var(--radius-sm); padding:16px; text-align:center; }
.sum-val { font-size:20px; font-weight:800; margin-bottom:4px; }
.sum-label { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; }

/* Sale row expand */
.sale-row { cursor:pointer; }
.sale-row:hover td { background:rgba(108,99,255,0.05) !important; }
.sale-detail { display:none; }
.sale-detail.open { display:table-row; }
.detail-inner { padding:12px 20px; background:rgba(0,0,0,0.2); }
.item-list { display:flex; flex-wrap:wrap; gap:8px; }
.item-pill {
    background:rgba(255,255,255,0.05); border:1px solid var(--card-border);
    border-radius:6px; padding:6px 12px; font-size:12px;
}
.item-pill strong { color:var(--accent); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-clock-history me-2" style="color:var(--accent)"></i>Sales History</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin:4px 0 0">
            Browse every sale from the beginning of your business
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Sale
        </a>
    </div>
</div>

{{-- ── View Mode Tabs ───────────────────────────────────────────────── --}}
<div class="view-tabs">
    <a href="{{ route('history.index', array_merge(request()->query(), ['view'=>'daily'])) }}"
       class="view-tab {{ $viewMode==='daily' ? 'active' : '' }}">
        <i class="bi bi-calendar-day me-1"></i>Daily
    </a>
    <a href="{{ route('history.index', array_merge(request()->query(), ['view'=>'monthly'])) }}"
       class="view-tab {{ $viewMode==='monthly' ? 'active' : '' }}">
        <i class="bi bi-calendar-month me-1"></i>Monthly
    </a>
    <a href="{{ route('history.index', array_merge(request()->query(), ['view'=>'annual'])) }}"
       class="view-tab {{ $viewMode==='annual' ? 'active' : '' }}">
        <i class="bi bi-calendar3 me-1"></i>Annual
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">

        {{-- ── Filters ──────────────────────────────────────────────── --}}
        <div class="card mb-4">
            <div class="card-body py-3">
                <form class="d-flex gap-3 flex-wrap align-items-end" method="GET" action="{{ route('history.index') }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

                    @if($viewMode === 'daily')
                    <div>
                        <label class="form-label">Select Date</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ $dateStr }}" max="{{ today()->toDateString() }}">
                    </div>
                    @elseif($viewMode === 'monthly')
                    <div>
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select" style="width:130px">
                            @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select" style="width:110px">
                            @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    @elseif($viewMode === 'annual')
                    <div>
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select" style="width:110px">
                            @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(!auth()->user()->isSeller() && count($sellers) > 0)
                    <div>
                        <label class="form-label">Seller</label>
                        <select name="seller_id" class="form-select" style="width:160px">
                            <option value="">All Sellers</option>
                            @foreach($sellers as $s)
                            <option value="{{ $s->id }}" {{ $sellerId==$s->id?'selected':'' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>View
                    </button>
                    <a href="{{ route('history.index') }}" class="btn btn-outline-secondary">Reset</a>
                </form>
            </div>
        </div>

        {{-- ── Summary Cards ────────────────────────────────────────── --}}
        <div class="summary-grid">
            <div class="sum-card">
                <div class="sum-val" style="color:var(--accent)">{{ number_format($summary['total_sales']) }}</div>
                <div class="sum-label">Total Sales</div>
            </div>
            <div class="sum-card">
                <div class="sum-val" style="color:var(--success)">{{ number_format($summary['total_revenue']) }}</div>
                <div class="sum-label">Revenue (RWF)</div>
            </div>
            <div class="sum-card">
                <div class="sum-val" style="color:var(--warning)">{{ number_format($summary['total_discount']) }}</div>
                <div class="sum-label">Discounts (RWF)</div>
            </div>
            <div class="sum-card">
                <div class="sum-val" style="color:var(--success)">{{ $summary['completed'] }}</div>
                <div class="sum-label">Completed</div>
            </div>
            <div class="sum-card">
                <div class="sum-val" style="color:var(--danger)">{{ $summary['cancelled'] }}</div>
                <div class="sum-label">Cancelled</div>
            </div>
        </div>

        {{-- ── Sales Table ──────────────────────────────────────────── --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    @if($viewMode === 'daily')
                        Sales on {{ \Carbon\Carbon::parse($dateStr)->format('l, d F Y') }}
                    @elseif($viewMode === 'monthly')
                        Sales in {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                    @else
                        Sales in {{ $year }}
                    @endif
                </span>
                <span class="badge" style="background:var(--accent-soft);color:var(--accent)">
                    {{ $sales->total() }} records
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Sale #</th>
                            <th>Date & Time</th>
                            @if(!auth()->user()->isSeller())<th>Seller</th>@endif
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Net Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($sales as $sale)
                    <tr class="sale-row" onclick="toggleDetail({{ $sale->id }})">
                        <td>
                            <span style="font-family:monospace;color:var(--accent);font-weight:600">
                                {{ $sale->sale_number }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-secondary)">
                            <div>{{ $sale->created_at->format('d M Y') }}</div>
                            <div style="color:var(--text-muted)">{{ $sale->created_at->format('H:i') }}</div>
                        </td>
                        @if(!auth()->user()->isSeller())
                        <td style="font-size:13px">{{ $sale->seller->name }}</td>
                        @endif
                        <td style="font-size:13px">{{ $sale->customer_name ?? '—' }}</td>
                        <td>
                            <span class="badge" style="background:var(--accent-soft);color:var(--accent)">
                                {{ $sale->items->count() }} item{{ $sale->items->count() > 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td style="font-weight:700;color:var(--success)">
                            {{ number_format($sale->net_amount) }} RWF
                        </td>
                        <td>
                            <span class="badge bg-secondary" style="font-size:11px">
                                {{ ucfirst(str_replace('_',' ',$sale->payment_method)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sale->status_badge_color }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('sales.show', $sale) }}"
                               class="btn btn-sm btn-outline-secondary btn-icon"
                               onclick="event.stopPropagation()">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    {{-- Expandable detail row --}}
                    <tr class="sale-detail" id="detail-{{ $sale->id }}">
                        <td colspan="{{ auth()->user()->isSeller() ? 7 : 8 }}" style="padding:0">
                            <div class="detail-inner">
                                <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;font-weight:600">
                                    ITEMS IN THIS SALE
                                </div>
                                <div class="item-list">
                                    @foreach($sale->items as $item)
                                    <div class="item-pill">
                                        {{ $item->product->name }}
                                        × {{ $item->quantity }}
                                        — <strong>{{ number_format($item->subtotal) }} RWF</strong>
                                    </div>
                                    @endforeach
                                </div>
                                @if($sale->notes)
                                <div style="margin-top:10px;font-size:12px;color:var(--text-secondary)">
                                    <strong>Notes:</strong> {{ $sale->notes }}
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isSeller() ? 7 : 8 }}" class="text-center py-5" style="color:var(--text-muted)">
                            <i class="bi bi-clock-history" style="font-size:2.5rem;display:block;margin-bottom:10px;opacity:0.4"></i>
                            No sales found for this period.
                            @if($viewMode === 'daily')
                            <br>
                            <small>Try selecting a different date from the calendar.</small>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($sales->hasPages())
            <div class="card-body" style="border-top:1px solid var(--card-border)">
                {{ $sales->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- ── Right: Calendar ─────────────────────────────────────────── --}}
    <div class="col-md-4">
        <div class="calendar-wrap">
            <div class="cal-header">
                <h3>{{ date('F Y', mktime(0,0,0,$calendarMonth,1,$calendarYear)) }}</h3>
                <div class="cal-nav">
                    @php
                        $prevMonth = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->subMonth();
                        $nextMonth = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->addMonth();
                    @endphp
                    <a href="{{ route('history.index', array_merge(request()->query(), [
                        'view'  => 'daily',
                        'date'  => $prevMonth->format('Y-m-d'),
                        'month' => $prevMonth->month,
                        'year'  => $prevMonth->year,
                    ])) }}">‹</a>
                    <a href="{{ route('history.index', array_merge(request()->query(), [
                        'view'  => 'daily',
                        'date'  => today()->toDateString(),
                        'month' => now()->month,
                        'year'  => now()->year,
                    ])) }}">Today</a>
                    @if($nextMonth->lte(now()))
                    <a href="{{ route('history.index', array_merge(request()->query(), [
                        'view'  => 'daily',
                        'date'  => $nextMonth->format('Y-m-d'),
                        'month' => $nextMonth->month,
                        'year'  => $nextMonth->year,
                    ])) }}">›</a>
                    @endif
                </div>
            </div>

            <div class="cal-grid">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                <div class="cal-day-label">{{ $d }}</div>
                @endforeach

                @php
                    $firstDay = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1);
                    $daysInMonth = $firstDay->daysInMonth;
                    $startDow = $firstDay->dayOfWeek; // 0=Sun
                    $today = today()->toDateString();
                    $selectedDate = $dateStr;
                @endphp

                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $startDow; $i++)
                <div class="cal-day empty"></div>
                @endfor

                {{-- Day cells --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey  = sprintf('%04d-%02d-%02d', $calendarYear, $calendarMonth, $day);
                    $hasSales = isset($calendarData[$dateKey]);
                    $count    = $hasSales ? $calendarData[$dateKey]->count : 0;
                    $isToday  = $dateKey === $today;
                    $isSelected = $dateKey === $selectedDate && $viewMode === 'daily';
                    $isFuture = $dateKey > $today;
                @endphp
                @if($isFuture)
                <div class="cal-day" style="opacity:0.2;cursor:default">
                    <span class="day-num">{{ $day }}</span>
                </div>
                @else
                <a href="{{ route('history.index', ['view'=>'daily','date'=>$dateKey,'seller_id'=>$sellerId]) }}"
                   class="cal-day {{ $hasSales ? 'has-sales' : '' }} {{ $isSelected ? 'selected' : '' }} {{ $isToday ? 'today' : '' }}"
                   title="{{ $hasSales ? $count.' sale(s) — '.number_format($calendarData[$dateKey]->revenue).' RWF' : 'No sales' }}">
                    <span class="day-num">{{ $day }}</span>
                    @if($hasSales)
                    <span class="day-count">{{ $count }}s</span>
                    @endif
                </a>
                @endif
                @endfor
            </div>

            {{-- Legend --}}
            <div class="d-flex gap-3 mt-3" style="font-size:11px;color:var(--text-muted)">
                <div class="d-flex align-items-center gap-1">
                    <div style="width:10px;height:10px;border-radius:3px;background:rgba(108,99,255,0.3)"></div>
                    Has sales
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:10px;height:10px;border-radius:3px;background:var(--accent)"></div>
                    Selected
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:10px;height:10px;border-radius:3px;border:1px solid var(--warning)"></div>
                    Today
                </div>
            </div>
        </div>

        {{-- Quick jump to yesterday --}}
        <div class="card">
            <div class="card-header" style="font-size:13px">Quick Navigation</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('history.index', ['view'=>'daily','date'=>today()->toDateString()]) }}"
                   class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-calendar-check me-2"></i>Today — {{ today()->format('d M Y') }}
                </a>
                <a href="{{ route('history.index', ['view'=>'daily','date'=>today()->subDay()->toDateString()]) }}"
                   class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-calendar-minus me-2"></i>Yesterday — {{ today()->subDay()->format('d M Y') }}
                </a>
                <a href="{{ route('history.index', ['view'=>'monthly','month'=>now()->month,'year'=>now()->year]) }}"
                   class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-calendar-month me-2"></i>This Month — {{ now()->format('F Y') }}
                </a>
                <a href="{{ route('history.index', ['view'=>'monthly','month'=>now()->subMonth()->month,'year'=>now()->subMonth()->year]) }}"
                   class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-calendar2-minus me-2"></i>Last Month — {{ now()->subMonth()->format('F Y') }}
                </a>
                <a href="{{ route('history.index', ['view'=>'annual','year'=>now()->year]) }}"
                   class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-calendar3 me-2"></i>This Year — {{ now()->year }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    row.classList.toggle('open');
}
</script>
@endpush
