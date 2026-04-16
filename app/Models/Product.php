<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'description', 'price', 'cost_price',
        'stock_quantity', 'low_stock_threshold', 'image',
        'is_active', 'category_id', 'created_by',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'cost_price'          => 'decimal:2',
        'is_active'           => 'boolean',
        'stock_quantity'      => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    // A product belongs to one category (many-to-1)
    public function category()    { return $this->belongsTo(Category::class); }
    // A product appears in many sale items (1-to-many)
    public function saleItems()   { return $this->hasMany(SaleItem::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity === 0;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price == 0) return 0;
        return round((($this->price - $this->cost_price) / $this->cost_price) * 100, 2);
    }

    public static function generateSku(): string
    {
        return 'PRD-' . strtoupper(uniqid());
    }
}
