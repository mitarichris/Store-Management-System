@extends('layouts.app')
@section('title',$category->name)
@section('content')
<div class="page-header">
    <h1>{{ $category->name }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_categories'))
        <a href="{{ route('categories.edit',$category) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        @endif
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>
<div class="card">
    <div class="card-header">Products in "{{ $category->name }}" ({{ $category->products->count() }})</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($category->products as $p)
            <tr>
                <td><a href="{{ route('products.show',$p) }}" style="color:var(--accent)">{{ $p->name }}</a></td>
                <td>{{ number_format($p->price) }} RWF</td>
                <td>{{ $p->stock_quantity }}</td>
                <td><span class="badge bg-{{ $p->is_active?'success':'secondary' }}">{{ $p->is_active?'Active':'Inactive' }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-4" style="color:var(--text-muted)">No products in this category.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
