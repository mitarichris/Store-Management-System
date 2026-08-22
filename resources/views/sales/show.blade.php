@extends('layouts.app')
@section('title', 'Sale ' . $sale->sale_number)
@section('breadcrumb', 'Sales / <span>' . $sale->sale_number . '</span>')

@section('content')
<div class="page-header">
    <h1>{{ $sale->sale_number }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_sales'))
        <a href="{{ route('sales.edit',$sale) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i><span data-i18n="sales_edit_status">Edit Status</span></a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
        <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-outline-success" target="_blank">
    <i class="bi bi-printer-fill me-1"></i><span data-i18n="sales_print_receipt">Print Receipt</span>
</a>
    </div>
</div>

<div class="row g-4">
    <!-- Receipt -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i><span data-i18n="sales_receipt">Sale Receipt</span></span>
                <span class="badge bg-{{ $sale->status_badge_color }}">{{ ucfirst($sale->status) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-scroll">
                <table class="table mb-0">
                    <thead><tr><th data-i18n="products_col_product">Product</th><th data-i18n="products_col_category">Category</th><th data-i18n="sales_unit_price">Unit Price</th><th data-i18n="sales_qty">Qty</th><th data-i18n="sales_subtotal">Subtotal</th></tr></thead>
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
            </div>
            <div class="card-body" style="border-top:1px solid var(--card-border)">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        @foreach([['Subtotal','total_amount','sales_subtotal'],['Discount','discount','sales_col_discount'],['Tax','tax','sales_tax']] as [$label,$field,$labelKey])
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px">
                            <span style="color:var(--text-muted)" data-i18n="{{ $labelKey }}">{{ $label }}</span>
                            <span>{{ number_format($sale->$field) }} RWF</span>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between mt-2 pt-2" style="border-top:1px solid var(--card-border);font-size:18px;font-weight:800">
                            <span data-i18n="sales_net_total">Net Total</span>
                            <span style="color:var(--success)">{{ number_format($sale->net_amount) }} RWF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details sidebar -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header" data-i18n="sales_info">Sale Info</div>
            <div class="card-body">
                @foreach([
                    ['hist_col_num', $sale->sale_number, 'Sale Number'],
                    ['sales_date', $sale->created_at->format('d M Y H:i'), 'Date'],
                    ['sales_payment', ucfirst(str_replace('_',' ',$sale->payment_method)), 'Payment'],
                    ['sales_col_customer', $sale->customer_name ?? '—', 'Customer'],
                    ['sales_phone', $sale->customer_phone ?? '—', 'Phone'],
                    ['sales_processed_by', $sale->seller->name, 'Processed by'],
                ] as [$labelKey,$val,$fallback])
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)" data-i18n="{{ $labelKey }}">{{ $fallback }}</span>
                    <span style="font-weight:600;max-width:60%;text-align:right">{{ $val }}</span>
                </div>
                @endforeach
                @if($sale->notes)
                <div class="mt-3 p-3" style="background:rgba(255,255,255,0.03);border-radius:8px;font-size:13px;color:var(--text-secondary)">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px" data-i18n="sales_notes_label">NOTES</div>
                    {{ $sale->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
