@extends('layouts.app')
@section('title', $user->name)
@section('breadcrumb', 'Users / <span>' . e($user->name) . '</span>')

@section('content')
<div class="page-header">
    <h1>{{ $user->name }}</h1>
    <div class="page-actions">
        @if(auth()->user()->hasPermission('edit_users'))
        <a href="{{ route('users.edit',$user) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        @endif
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div style="width:72px;height:72px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;margin:0 auto 16px">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:4px">{{ $user->name }}</h3>
                <div style="color:var(--text-muted);font-size:13px;margin-bottom:12px">{{ $user->email }}</div>
                <span class="badge bg-{{ $user->role_badge_color }} mb-2">{{ $user->role_label }}</span>
                <br>
                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="card-body" style="border-top:1px solid var(--card-border)">
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">Phone</span>
                    <span>{{ $user->phone ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">Created by</span>
                    <span>{{ $user->creator?->name ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:13px">
                    <span style="color:var(--text-muted)">Member since</span>
                    <span>{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Sales History ({{ $user->sales->count() }})</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Sale #</th><th>Items</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($user->sales->take(10) as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show',$sale) }}" style="color:var(--accent);font-family:monospace">{{ $sale->sale_number }}</a></td>
                        <td>{{ $sale->items->count() }}</td>
                        <td style="font-weight:700">{{ number_format($sale->net_amount) }} RWF</td>
                        <td><span class="badge bg-{{ $sale->status_badge_color }}">{{ ucfirst($sale->status) }}</span></td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $sale->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4" style="color:var(--text-muted)">No sales recorded.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
