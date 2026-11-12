@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="page-header">
    <h1>{{ $product->name }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_products'))
        <a href="{{ route('products.edit',$product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i><span data-i18n="edit">Edit</span></a>
        @endif
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4 col-md-5">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge" style="background:rgba(56,189,248,0.15);color:#38bdf8">{{ $product->category->name }}</span>
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}"><span data-i18n="{{ $product->is_active ? 'active' : 'inactive' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></span>
                </div>
                <div style="font-size:28px;font-weight:800;color:var(--accent);margin-bottom:4px">{{ number_format($product->price) }} RWF</div>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px" data-i18n="products_selling_price">Selling price</div>
                @foreach([['Cost Price','cost_price','RWF','products_cost_price'],['Stock','stock_quantity','units','products_stock'],['Low Stock Alert','low_stock_threshold','units','products_low_alert'],['SKU','sku','','products_sku']] as [$label,$key,$unit,$labelKey])
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)" data-i18n="{{ $labelKey }}">{{ $label }}</span>
                    <span style="font-weight:600">{{ $key === 'sku' ? $product->$key : number_format($product->$key) }} {{ $unit }}</span>
                </div>
                @endforeach
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)" data-i18n="products_profit_margin">Profit margin</span>
                    <span style="font-weight:600;color:var(--success)">{{ $product->profit_margin }}%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-md-7">
        <div class="card mb-4">
            <div class="card-header" data-i18n="products_sales_stats">Sales Stats</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><div class="stat-card"><div class="stat-icon" style="background:rgba(108,99,255,0.15);color:var(--accent)"><i class="bi bi-bag-check-fill"></i></div><div class="stat-info"><div class="stat-value">{{ number_format($totalSold) }}</div><div class="stat-label" data-i18n="products_units_sold">Units Sold</div></div></div></div>
                    <div class="col-6"><div class="stat-card"><div class="stat-icon" style="background:rgba(34,197,94,0.15);color:var(--success)"><i class="bi bi-cash-coin"></i></div><div class="stat-info"><div class="stat-value">{{ number_format($revenue) }}</div><div class="stat-label" data-i18n="dash_revenue_rwf">Revenue (RWF)</div></div></div></div>
                </div>
            </div>
        </div>
        @if($product->description)
        <div class="card">
            <div class="card-header" data-i18n="products_description">Description</div>
            <div class="card-body" style="font-size:13.5px;color:var(--text-secondary);line-height:1.7">{{ $product->description }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
