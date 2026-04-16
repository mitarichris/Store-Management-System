<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    // A sale item belongs to one sale and one product
    public function sale()    { return $this->belongsTo(Sale::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
