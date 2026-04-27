<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCommission extends Model
{
    protected $fillable = [
        'order_id', 'gross_amount', 'tax_amount', 'platform_amount',
        'vendor_amount', 'tax_rate', 'platform_rate', 'status', 'distributed_at',
    ];

    protected $casts = [
        'gross_amount'    => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'platform_amount' => 'decimal:2',
        'vendor_amount'   => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'platform_rate'   => 'decimal:2',
        'distributed_at'  => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function getVendorRateAttribute(): float
    {
        return round(100 - (float) $this->platform_rate, 2);
    }
}
