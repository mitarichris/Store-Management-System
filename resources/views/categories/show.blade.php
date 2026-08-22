@extends('layouts.app')
@section('title',$category->name)
@section('content')
<div class="page-header">
    <h1>{{ $category->name }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_categories'))
        <a href="{{ route('categories.edit',$category) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i><span data-i18n="edit">Edit</span></a>
        @endif
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
    </div>
</div>
<div class="card">
    <div class="card-header"><span data-i18n="cats_products_in">Products in</span> "{{ $category->name }}" ({{ $category->products->count() }})</div>
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead><tr><th data-i18n="categories_col_name">Name</th><th data-i18n="products_col_price">Price</th><th data-i18n="products_col_stock">Stock</th><th data-i18n="users_col_status">Status</th></tr></thead>
            <tbody>
            @forelse($category->products as $p)
            <tr>
                <td><a href="{{ route('products.show',$p) }}" style="color:var(--accent)">{{ $p->name }}</a></td>
                <td>{{ number_format($p->price) }} RWF</td>
                <td>{{ $p->stock_quantity }}</td>
                <td><span class="badge bg-{{ $p->is_active?'success':'secondary' }}"><span data-i18n="{{ $p->is_active?'active':'inactive' }}">{{ $p->is_active?'Active':'Inactive' }}</span></span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-4" style="color:var(--text-muted)"><span data-i18n="categories_no_products">No products in this category.</span></td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
