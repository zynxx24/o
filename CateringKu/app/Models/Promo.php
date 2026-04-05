<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    protected $primaryKey = 'promo_id';

    protected $fillable = [
        'promo_code', 'promo_name', 'description', 'discount_type',
        'discount_value', 'min_order', 'max_discount', 'vendor_id',
        'valid_from', 'valid_until', 'usage_limit', 'used_count', 'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    public function calculateDiscount(float $orderAmount): float
    {
        $discount = $this->discount_type === 'percentage'
            ? ($orderAmount * $this->discount_value) / 100
            : $this->discount_value;

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }
}
