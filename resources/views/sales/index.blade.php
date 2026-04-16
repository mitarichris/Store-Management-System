@extends('layouts.app')
@section('title','Sales')
@section('breadcrumb','Sales / <span>All Sales</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-receipt-cutoff me-2" style="color:var(--accent)"></i>Sales</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_sales'))
        <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Sale</a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Sale # or customer…" value="{{ request('search') }}" style="width:200px">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select" style="width:140px">
                    <option value="">All</option>
                    @foreach(['completed','pending','cancelled','refunded'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>Sale #</th><th>Customer</th><th>Seller</th><th>Total</th><th>Discount</th><th>Net Amount</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
            <tr>
                <td><a href="{{ route('sales.show',$sale) }}" style="color:var(--accent);font-family:monospace;font-weight:600">{{ $sale->sale_number }}</a></td>
                <td>
                    <div style="font-size:13px">{{ $sale->customer_name ?? '—' }}</div>
                    @if($sale->customer_phone)<div style="font-size:11px;color:var(--text-muted)">{{ $sale->customer_phone }}</div>@endif
                </td>
                <td style="font-size:13px">{{ $sale->seller->name }}</td>
                <td>{{ number_format($sale->total_amount) }}</td>
                <td style="color:var(--danger)">{{ $sale->discount > 0 ? '-'.number_format($sale->discount) : '—' }}</td>
                <td style="font-weight:700;color:var(--success)">{{ number_format($sale->net_amount) }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$sale->payment_method)) }}</span></td>
                <td><span class="badge bg-{{ $sale->status_badge_color }}">{{ ucfirst($sale->status) }}</span></td>
                <td style="font-size:12px;color:var(--text-muted)">{{ $sale->created_at->format('d M Y H:i') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('sales.show',$sale) }}" class="btn btn-sm btn-outline-secondary btn-icon"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasPermission('edit_sales'))
                        <a href="{{ route('sales.edit',$sale) }}" class="btn btn-sm btn-outline-primary btn-icon"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_sales'))
                        <form method="POST" action="{{ route('sales.destroy',$sale) }}" onsubmit="return confirm('Delete sale {{ $sale->sale_number }}? Stock will be restored.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center py-5" style="color:var(--text-muted)">No sales found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
    <div class="card-body" style="border-top:1px solid var(--card-border)">{{ $sales->links() }}</div>
    @endif
</div>
@endsection
