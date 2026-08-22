@extends('layouts.app')
@section('title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content')
<div class="page-header">
    <h1><span data-i18n="{{ isset($category) ? 'cats_edit' : 'cats_add' }}">{{ isset($category) ? 'Edit Category' : 'Add Category' }}</span></h1>
    <div class="page-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
    </div>
</div>
<div style="max-width:560px">
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ isset($category) ? route('categories.update',$category) : route('categories.store') }}">
            @csrf
            @if(isset($category)) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" data-i18n="categories_name">Category Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" data-i18n="categories_col_desc">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" data-i18n="users_status_label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $category->is_active ?? 1) ? 'selected' : '' }} data-i18n="active">Active</option>
                        <option value="0" {{ !old('is_active', $category->is_active ?? 1) ? 'selected' : '' }} data-i18n="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <span data-i18n="{{ isset($category) ? 'cats_update' : 'cats_add' }}">{{ isset($category) ? 'Update Category' : 'Add Category' }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
