<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'user_id', 'vendor_id', 'order_number', 'order_type', 'event_type',
        'event_date', 'event_time', 'delivery_address', 'delivery_city',
        'num_people', 'subtotal', 'tax', 'delivery_fee', 'discount',
        'total_amount', 'status', 'payment_status', 'special_request', 'cancellation_reason',
    ];

    protected $casts = [
        'event_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class, 'order_id', 'order_id');
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'ORD-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
