<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'sale_id', 'product_id', 'type',
        'amount', 'balance_after', 'description', 'transaction_date',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'balance_after'    => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function sale()    { return $this->belongsTo(Sale::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'opening_balance'    => 'Opening Balance',
            'inventory_purchase' => 'Inventory Purchase',
            'sale_revenue'       => 'Sale Revenue',
            default              => ucfirst($this->type),
        };
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'opening_balance'    => 'info',
            'inventory_purchase' => 'danger',
            'sale_revenue'       => 'success',
            default              => 'secondary',
        };
    }

    // Is this transaction money coming in or going out?
    public function isCredit(): bool
    {
        return in_array($this->type, ['opening_balance', 'sale_revenue']);
    }

    public function isDebit(): bool
    {
        return $this->type === 'inventory_purchase';
    }

    // Scopes for filtering
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPeriod($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    public function scopeByDay($query, $date)
    {
        return $query->whereDate('transaction_date', $date);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('transaction_date', $year)
                     ->whereMonth('transaction_date', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('transaction_date', $year);
    }
}