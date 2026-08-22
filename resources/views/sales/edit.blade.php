@extends('layouts.app')
@section('title', 'Edit Sale ' . $sale->sale_number)

@section('content')
<div class="page-header">
    <h1 data-i18n="sales_edit_status">Edit Sale Status</h1>
    <div class="page-actions">
        <a href="{{ route('sales.show',$sale) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
    </div>
</div>
<div style="max-width:480px">
<div class="card">
    <div class="card-header">{{ $sale->sale_number }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('sales.update',$sale) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label" data-i18n="users_col_status">Status *</label>
                <select name="status" class="form-select" required>
                    @foreach(['completed','pending','cancelled','refunded'] as $s)
                    <option value="{{ $s }}" {{ $sale->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" data-i18n="sales_notes">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ $sale->notes }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><span data-i18n="sales_update">Update Sale</span></button>
        </form>
    </div>
</div>
</div>
@endsection
