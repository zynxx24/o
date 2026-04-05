<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $primaryKey = 'vendor_id';

    protected $fillable = [
        'user_id', 'vendor_name', 'description', 'address', 'city', 'province',
        'postal_code', 'phone', 'email', 'logo_url', 'rating', 'total_reviews', 'status',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'vendor_id', 'vendor_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'vendor_id', 'vendor_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id', 'vendor_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'vendor_id', 'vendor_id');
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class, 'vendor_id', 'vendor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
