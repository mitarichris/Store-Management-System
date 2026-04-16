@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="page-header">
    <h1>{{ $product->name }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_products'))
        <a href="{{ route('products.edit',$product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        @endif
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge" style="background:rgba(56,189,248,0.15);color:#38bdf8">{{ $product->category->name }}</span>
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div style="font-size:28px;font-weight:800;color:var(--accent);margin-bottom:4px">{{ number_format($product->price) }} RWF</div>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px">Selling price</div>
                @foreach([['Cost Price','cost_price','RWF'],['Stock','stock_quantity','units'],['Low Stock Alert','low_stock_threshold','units'],['SKU','sku','']] as [$label,$key,$unit])
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span style="font-weight:600">{{ number_format($product->$key) }} {{ $unit }}</span>
                </div>
                @endforeach
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">Profit margin</span>
                    <span style="font-weight:600;color:var(--success)">{{ $product->profit_margin }}%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Sales Stats</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><div class="stat-card"><div class="stat-icon" style="background:rgba(108,99,255,0.15);color:var(--accent)"><i class="bi bi-bag-check-fill"></i></div><div class="stat-info"><div class="stat-value">{{ number_format($totalSold) }}</div><div class="stat-label">Units Sold</div></div></div></div>
                    <div class="col-6"><div class="stat-card"><div class="stat-icon" style="background:rgba(34,197,94,0.15);color:var(--success)"><i class="bi bi-cash-coin"></i></div><div class="stat-info"><div class="stat-value">{{ number_format($revenue) }}</div><div class="stat-label">Revenue (RWF)</div></div></div></div>
                </div>
            </div>
        </div>
        @if($product->description)
        <div class="card">
            <div class="card-header">Description</div>
            <div class="card-body" style="font-size:13.5px;color:var(--text-secondary);line-height:1.7">{{ $product->description }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
