@extends('layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('breadcrumb', 'Products / <span>' . (isset($product) ? 'Edit' : 'Add') . '</span>')

@section('content')
<div class="page-header">
    <h1>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h1>
    <div class="page-actions">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>
<div style="max-width:700px">
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ isset($product) ? route('products.update',$product) : route('products.store') }}">
            @csrf
            @if(isset($product)) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select category…</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Selling Price (RWF) *</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price', $product->price ?? '') }}" min="0" step="0.01" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cost Price (RWF)</label>
                    <input type="number" name="cost_price" class="form-control"
                        value="{{ old('cost_price', $product->cost_price ?? 0) }}" min="0" step="0.01">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror"
                        value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0" required>
                    @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Low Stock Threshold *</label>
                    <input type="number" name="low_stock_threshold" class="form-control"
                        value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $product->is_active ?? 1) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $product->is_active ?? 1) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-{{ isset($product) ? 'check-lg' : 'plus-lg' }} me-1"></i>
                        {{ isset($product) ? 'Update Product' : 'Add Product' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
