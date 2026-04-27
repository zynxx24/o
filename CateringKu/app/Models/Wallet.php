<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $primaryKey = 'wallet_id';

    protected $fillable = [
        'user_id', 'balance', 'frozen_balance', 'total_earned', 'total_withdrawn',
    ];

    protected $casts = [
        'balance'          => 'decimal:2',
        'frozen_balance'   => 'decimal:2',
        'total_earned'     => 'decimal:2',
        'total_withdrawn'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id', 'wallet_id');
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class, 'wallet_id', 'wallet_id');
    }

    /** Cek apakah ada withdraw request yang masih pending */
    public function hasPendingWithdrawal(): bool
    {
        return $this->withdrawalRequests()->where('status', 'pending')->exists();
    }
}
