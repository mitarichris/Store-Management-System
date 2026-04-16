@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Create User')
@section('breadcrumb', 'Users / <span>' . (isset($user) ? 'Edit' : 'Create') . '</span>')

@section('content')
<div class="page-header">
    <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
    <div class="page-actions">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div style="max-width:600px">
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ isset($user) ? route('users.update',$user) : route('users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $user->phone ?? '') }}" placeholder="+250788...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role ?? '') === $role ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_',' ',$role)) }}
                        </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if(isset($user))
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ ($user->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($user->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Password {{ isset($user) ? '(leave blank to keep)' : '*' }}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        {{ isset($user) ? '' : 'required' }}>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Min 8 chars, uppercase, lowercase & number</div>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-{{ isset($user) ? 'check-lg' : 'plus-lg' }} me-1"></i>
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
