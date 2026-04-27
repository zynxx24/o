<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Vendor extends Model
{
    protected $primaryKey = 'vendor_id';

    protected $fillable = [
        'user_id',
        'vendor_name',
        'description',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'whatsapp_number',
        'email',
        'logo_url',
        'rating',
        'total_reviews',
        'status',
    ];

    protected $appends = ['slug'];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    /**
     * Generate URL-safe slug: "vendor-name-base36id"
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->vendor_name) . '-' . base_convert($this->vendor_id, 10, 36);
    }

    /**
     * Resolve vendor from slug — extract base36 ID from the last segment.
     */
    public static function findBySlug(string $slug): self
    {
        $parts = explode('-', $slug);
        $encoded = array_pop($parts);
        $id = (int) base_convert($encoded, 36, 10);

        return static::findOrFail($id);
    }

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

    /**
     * Komisi dari semua order vendor (melalui relasi orders).
     * OrderCommission tidak punya vendor_id langsung — akses lewat orders.
     */
    public function orderCommissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            OrderCommission::class,
            Order::class,
            'vendor_id',   // FK pada Order → Vendor
            'order_id',    // FK pada OrderCommission → Order
            'vendor_id',   // PK lokal Vendor
            'order_id'     // PK lokal Order
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
