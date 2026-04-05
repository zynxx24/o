<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    protected $primaryKey = 'package_id';

    protected $fillable = [
        'vendor_id', 'package_name', 'description', 'price_per_person',
        'min_person', 'max_person', 'package_type', 'image_url', 'is_available',
    ];

    protected $casts = [
        'price_per_person' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'package_items', 'package_id', 'item_id')
            ->withPivot('quantity');
    }
}
