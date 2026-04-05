<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'vendor_id', 'category_id', 'item_name', 'description', 'price',
        'min_order', 'unit', 'image_url', 'is_available', 'preparation_time', 'spicy_level',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
