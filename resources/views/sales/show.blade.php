@extends('layouts.app')
@section('title', 'Sale ' . $sale->sale_number)
@section('breadcrumb', 'Sales / <span>' . $sale->sale_number . '</span>')

@section('content')
<div class="page-header">
    <h1>{{ $sale->sale_number }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_sales'))
        <a href="{{ route('sales.edit',$sale) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit Status</a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-success" target="_blank">
    <i class="bi bi-printer-fill me-1"></i>Print Receipt
</a>
    </div>
</div>

<div class="row g-4">
    <!-- Receipt -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Sale Receipt</span>
                <span class="badge bg-{{ $sale->status_badge_color }}">{{ ucfirst($sale->status) }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>Category</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td style="font-weight:600">{{ $item->product->name }}</td>
                        <td><span class="badge" style="background:rgba(56,189,248,0.15);color:#38bdf8">{{ $item->product->category->name }}</span></td>
                        <td>{{ number_format($item->unit_price) }} RWF</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="font-weight:700">{{ number_format($item->subtotal) }} RWF</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body" style="border-top:1px solid var(--card-border)">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        @foreach([['Subtotal','total_amount'],['Discount','discount'],['Tax','tax']] as [$label,$field])
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px">
                            <span style="color:var(--text-muted)">{{ $label }}</span>
                            <span>{{ number_format($sale->$field) }} RWF</span>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between mt-2 pt-2" style="border-top:1px solid var(--card-border);font-size:18px;font-weight:800">
                            <span>Net Total</span>
                            <span style="color:var(--success)">{{ number_format($sale->net_amount) }} RWF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details sidebar -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Sale Info</div>
            <div class="card-body">
                @foreach([
                    ['Sale Number', $sale->sale_number],
                    ['Date', $sale->created_at->format('d M Y H:i')],
                    ['Payment', ucfirst(str_replace('_',' ',$sale->payment_method))],
                    ['Customer', $sale->customer_name ?? '—'],
                    ['Phone', $sale->customer_phone ?? '—'],
                    ['Processed by', $sale->seller->name],
                ] as [$label,$val])
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span style="font-weight:600;max-width:60%;text-align:right">{{ $val }}</span>
                </div>
                @endforeach
                @if($sale->notes)
                <div class="mt-3 p-3" style="background:rgba(255,255,255,0.03);border-radius:8px;font-size:13px;color:var(--text-secondary)">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px">NOTES</div>
                    {{ $sale->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
