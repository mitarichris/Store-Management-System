@extends('layouts.app')
@section('title','Categories')
@section('breadcrumb','Inventory / <span>Categories</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-tag-fill me-2" style="color:var(--accent)"></i><span data-i18n="nav_categories">Categories</span></h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_categories'))
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i><span data-i18n="categories_add">Add Category</span></a>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form class="filter-form" method="GET">
            <div style="flex:1;min-width:150px;max-width:260px">
                <input type="text" name="search" class="form-control" data-i18n-placeholder="categories_search_ph" placeholder="Search categories…" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary"><span data-i18n="search">Search</span></button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><span data-i18n="reset">Reset</span></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead><tr><th data-i18n="categories_col_name">Category</th><th data-i18n="categories_col_desc">Description</th><th data-i18n="categories_col_products">Products</th><th data-i18n="users_col_status">Status</th><th data-i18n="users_col_created_by">Created By</th><th data-i18n="users_col_actions">Actions</th></tr></thead>
            <tbody>
            @forelse($categories as $category)
            <tr>
                <td style="font-weight:600">{{ $category->name }}</td>
                <td style="font-size:13px;color:var(--text-secondary)">{{ Str::limit($category->description, 60) ?? '—' }}</td>
                <td><span class="badge" style="background:var(--accent-soft);color:var(--accent)">{{ $category->products_count }} <span data-i18n="categories_products_count">products</span></span></td>
                <td><span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}"><span data-i18n="{{ $category->is_active ? 'active' : 'inactive' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></span></td>
                <td style="font-size:12px;color:var(--text-muted)">{{ $category->creator?->name ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('categories.show',$category) }}" class="btn btn-sm btn-outline-secondary btn-icon"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasPermission('edit_categories'))
                        <a href="{{ route('categories.edit',$category) }}" class="btn btn-sm btn-outline-primary btn-icon"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_categories'))
                        <form method="POST" action="{{ route('categories.destroy',$category) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-5" style="color:var(--text-muted)"><span data-i18n="categories_no_found">No categories found.</span></td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-body" style="border-top:1px solid var(--card-border)">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
