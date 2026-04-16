@extends('layouts.app')
@section('title','Users')
@section('breadcrumb','Management / <span>Users</span>')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people-fill me-2" style="color:var(--accent)"></i>Users</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('create_users'))
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add User</a>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email…" value="{{ request('search') }}" style="width:220px">
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-select" style="width:160px">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role')=='super_admin'?'selected':'' }}>Super Admin</option>
                    <option value="shop_owner" {{ request('role')=='shop_owner'?'selected':'' }}>Shop Owner</option>
                    <option value="seller" {{ request('role')=='seller'?'selected':'' }}>Seller</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>User</th><th>Role</th><th>Phone</th><th>Status</th><th>Created By</th><th>Joined</th><th>Actions</th>
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
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="font-size:12.5px;color:var(--text-secondary)">{{ $user->creator?->name ?? '—' }}</td>
                <td style="font-size:12px;color:var(--text-muted)">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary btn-icon" title="View"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasPermission('edit_users'))
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
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
                    No users found.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-body border-top" style="border-color:var(--card-border)!important">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
