@extends('layouts.app')
@section('title','Categories')
@section('breadcrumb','Inventory / <span>Categories</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-tag-fill me-2" style="color:var(--accent)"></i>Categories</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_categories'))
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3">
            <input type="text" name="search" class="form-control" placeholder="Search categories…" value="{{ request('search') }}" style="max-width:260px">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Category</th><th>Description</th><th>Products</th><th>Status</th><th>Created By</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($categories as $category)
            <tr>
                <td style="font-weight:600">{{ $category->name }}</td>
                <td style="font-size:13px;color:var(--text-secondary)">{{ Str::limit($category->description, 60) ?? '—' }}</td>
                <td><span class="badge" style="background:var(--accent-soft);color:var(--accent)">{{ $category->products_count }} products</span></td>
                <td><span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
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
            <tr><td colspan="6" class="text-center py-5" style="color:var(--text-muted)">No categories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-body" style="border-top:1px solid var(--card-border)">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
