<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorApplication extends Model
{
    protected $fillable = [
        'user_id', 'vendor_name', 'description', 'address', 'city', 'province',
        'phone', 'email', 'deposit_amount', 'payment_proof', 'payment_method',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'reviewed_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['submitted', 'deposit_pending']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
