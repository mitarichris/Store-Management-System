<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'is_active', 'phone', 'avatar', 'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Roles ──────────────────────────────────────────────────────────────
    public function isSuperAdmin(): bool  { return $this->role === 'super_admin'; }
    public function isShopOwner(): bool   { return $this->role === 'shop_owner'; }
    public function isSeller(): bool      { return $this->role === 'seller'; }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'shop_owner'  => 'Shop Owner',
            'seller'      => 'Seller',
            default       => ucfirst($this->role),
        };
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'danger',
            'shop_owner'  => 'warning',
            'seller'      => 'info',
            default       => 'secondary',
        };
    }

    // ── Permissions ────────────────────────────────────────────────────────
    public function hasPermission(string $permission): bool
    {
        $permissions = [
            'super_admin' => [
                'manage_users', 'view_users', 'create_users', 'edit_users', 'delete_users',
                'manage_products', 'view_products', 'create_products', 'edit_products', 'delete_products',
                'manage_categories', 'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
                'manage_sales', 'view_sales', 'create_sales', 'edit_sales', 'delete_sales',
                'view_reports', 'view_revenue', 'view_dashboard',
            ],
            'shop_owner' => [
                'manage_products', 'view_products', 'create_products', 'edit_products', 'delete_products',
                'manage_categories', 'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
                'manage_sales', 'view_sales', 'create_sales', 'edit_sales', 'delete_sales',
                'view_users', 'create_users', 'edit_users', 'delete_users',
                'view_reports', 'view_revenue', 'view_dashboard',
            ],
            'seller' => [
                'view_products',
                'view_sales', 'create_sales',
                'view_dashboard',
            ],
            
        ];

        return in_array($permission, $permissions[$this->role] ?? []);
    }

    // ── Relationships ──────────────────────────────────────────────────────
    public function sales()         { return $this->hasMany(Sale::class, 'seller_id'); }
    public function products()      { return $this->hasMany(Product::class, 'created_by'); }
    public function categories()    { return $this->hasMany(Category::class, 'created_by'); }
    public function createdUsers()  { return $this->hasMany(User::class, 'created_by'); }
    public function creator()       { return $this->belongsTo(User::class, 'created_by'); }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeRole($query, string $role) { return $query->where('role', $role); }
    public function scopeActive($query)             { return $query->where('is_active', true); }
}
