<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id', 'opening_balance', 'current_balance', 'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Total stock purchase cost recorded manually
    public function totalInventoryCost(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('type', 'inventory_purchase')
            ->sum('amount');
    }

    // Revenue = profit only (selling price - cost price), auto-recorded from sales
    public function totalRevenue(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('type', 'sale_revenue')
            ->sum('amount');
    }

    // Current Balance = Opening Capital + Revenue (profit earned)
    // Example: Capital=100,000 | sold profit=10,000 → Balance = 110,000
    // The inventory cost does NOT reduce the balance — it is just for tracking
    public function calculatedBalance(): float
    {
        return (float) $this->opening_balance + $this->totalRevenue();
    }

    public function isProfitable(): bool
    {
        return $this->totalRevenue() >= 0;
    }
}
