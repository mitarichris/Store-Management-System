@extends('layouts.app')
@section('title','Products')
@section('breadcrumb','Inventory / <span>Products</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-box-seam-fill me-2" style="color:var(--accent)"></i><span data-i18n="products_title">Products</span></h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_products'))
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i><span data-i18n="products_add">Add Product</span></a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="filter-form" method="GET">
            <div style="flex:1;min-width:140px">
                <label class="form-label" data-i18n="search">Search</label>
                <input type="text" name="search" class="form-control" data-i18n-placeholder="products_search_ph" placeholder="Name or SKU…" value="{{ request('search') }}">
            </div>
            <div style="flex:1;min-width:130px;max-width:180px">
                <label class="form-label" data-i18n="products_category">Category</label>
                <select name="category" class="form-select">
                    <option value="" data-i18n="products_all_categories">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:120px;max-width:160px">
                <label class="form-label" data-i18n="products_stock">Stock</label>
                <select name="stock" class="form-select">
                    <option value="" data-i18n="products_all_stock">All Stock</option>
                    <option value="low" {{ request('stock')=='low'?'selected':'' }} data-i18n="products_low_stock">Low Stock</option>
                    <option value="out" {{ request('stock')=='out'?'selected':'' }} data-i18n="products_out_of_stock">Out of Stock</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><span data-i18n="filter">Filter</span></button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><span data-i18n="reset">Reset</span></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead>
                <tr><th data-i18n="products_col_product">Product</th><th data-i18n="products_col_sku">SKU</th><th data-i18n="products_col_category">Category</th><th data-i18n="products_col_price">Price</th><th data-i18n="products_col_cost">Cost</th><th data-i18n="products_col_stock">Stock</th><th data-i18n="products_col_status">Status</th><th data-i18n="products_col_actions">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:13.5px">{{ $product->name }}</div>
                    @if($product->description)
                    <div style="font-size:11px;color:var(--text-muted)">{{ Str::limit($product->description,50) }}</div>
                    @endif
                </td>
                <td style="font-family:monospace;font-size:12px;color:var(--text-muted)">{{ $product->sku }}</td>
                <td>
                    <span class="badge" style="background:rgba(56,189,248,0.15);color:#38bdf8">{{ $product->category->name }}</span>
                </td>
                <td style="font-weight:700">{{ number_format($product->price) }}</td>
                <td style="color:var(--text-secondary)">{{ number_format($product->cost_price) }}</td>
                <td>
                    @if($product->isOutOfStock())
                        <span class="badge bg-danger" data-i18n="products_out_of_stock">Out of Stock</span>
                    @elseif($product->isLowStock())
                        <span class="badge bg-warning text-dark"><span data-i18n="products_low_badge">Low</span>: {{ $product->stock_quantity }}</span>
                    @else
                        <span style="font-weight:600;color:var(--success)">{{ number_format($product->stock_quantity) }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}"><span data-i18n="{{ $product->is_active ? 'active' : 'inactive' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('products.show',$product) }}" class="btn btn-sm btn-outline-secondary btn-icon" data-i18n-title="view" title="View"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasPermission('edit_products'))
                        <a href="{{ route('products.edit',$product) }}" class="btn btn-sm btn-outline-primary btn-icon" data-i18n-title="edit" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_products'))
                        <form method="POST" action="{{ route('products.destroy',$product) }}" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:8px"></i><span data-i18n="products_no_found">No products found.</span>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-body" style="border-top:1px solid var(--card-border)">{{ $products->links() }}</div>
    @endif
</div>
@endsection
