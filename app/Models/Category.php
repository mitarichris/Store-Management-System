<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'is_active', 'created_by',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    // One category has many products (1-to-many)
    public function products()  { return $this->hasMany(Product::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }

    public function getProductCountAttribute(): int
    {
        return $this->products()->count();
    }
}
