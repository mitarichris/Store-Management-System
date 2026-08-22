@extends('layouts.app')
@section('title','Users')
@section('breadcrumb','Management / <span>Users</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people-fill me-2" style="color:var(--accent)"></i><span data-i18n="users_title">Users</span></h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_users'))
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i><span data-i18n="users_add">Add User</span></a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="filter-form" method="GET">
            <div style="flex:1;min-width:150px">
                <label class="form-label" data-i18n="search">Search</label>
                <input type="text" name="search" class="form-control" data-i18n-placeholder="users_search_ph" placeholder="Name or email…" value="{{ request('search') }}">
            </div>
            <div style="flex:1;min-width:130px;max-width:200px">
                <label class="form-label" data-i18n="users_role">Role</label>
                <select name="role" class="form-select">
                    <option value="" data-i18n="users_all_roles">All Roles</option>
                    <option value="super_admin" {{ request('role')=='super_admin'?'selected':'' }} data-i18n="users_super_admin">Super Admin</option>
                    <option value="shop_owner" {{ request('role')=='shop_owner'?'selected':'' }} data-i18n="users_shop_owner">Shop Owner</option>
                    <option value="seller" {{ request('role')=='seller'?'selected':'' }} data-i18n="users_seller_role">Seller</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><span data-i18n="filter">Filter</span></button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><span data-i18n="reset">Reset</span></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-scroll">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th data-i18n="users_col_user">User</th><th data-i18n="users_col_role">Role</th><th data-i18n="users_col_phone">Phone</th><th data-i18n="users_col_status">Status</th><th data-i18n="users_col_created_by">Created By</th><th data-i18n="users_col_joined">Joined</th><th data-i18n="users_col_actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13.5px">{{ $user->name }}</div>
                            <div style="font-size:11.5px;color:var(--text-muted)">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-{{ $user->role_badge_color }}">{{ $user->role_label }}</span>
                </td>
                <td style="color:var(--text-secondary);font-size:13px">{{ $user->phone ?? '—' }}</td>
                <td>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                        <span data-i18n="{{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </span>
                </td>
                <td style="font-size:12.5px;color:var(--text-secondary)">{{ $user->creator?->name ?? '—' }}</td>
                <td style="font-size:12px;color:var(--text-muted)">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary btn-icon" title="View" data-i18n-title="view"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasPermission('edit_users'))
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary btn-icon" title="Edit" data-i18n-title="edit"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_users') && $user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px"></i>
                    <span data-i18n="users_no_found">No users found.</span>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-body border-top" style="border-color:var(--card-border)!important">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
