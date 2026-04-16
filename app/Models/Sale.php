<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_number', 'total_amount', 'discount', 'tax', 'net_amount',
        'payment_method', 'status', 'customer_name', 'customer_phone',
        'notes', 'seller_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'net_amount'   => 'decimal:2',
    ];

    // A sale has many sale items (1-to-many)
    public function items()  { return $this->hasMany(SaleItem::class); }
    // A sale belongs to one seller
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }

   public static function generateSaleNumber(): string
{
    $latest = static::withTrashed()->orderBy('id', 'desc')->first();
    $next   = $latest ? (int) substr($latest->sale_number, 4) + 1 : 1;
    return 'SAL-' . str_pad($next, 6, '0', STR_PAD_LEFT);
}

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending'   => 'warning',
            'cancelled' => 'danger',
            'refunded'  => 'secondary',
            default     => 'light',
        };
    }
}
