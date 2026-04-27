<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    protected $fillable = ['name', 'tax_rate', 'platform_rate', 'is_active'];

    protected $casts = [
        'tax_rate'      => 'decimal:2',
        'platform_rate' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    /** Persentase yang masuk ke vendor (sisa setelah platform fee) */
    public function getVendorRateAttribute(): float
    {
        return round(100 - (float) $this->platform_rate, 2);
    }

    /** Ambil aturan aktif, fallback ke default */
    public static function active(): static
    {
        return static::where('is_active', true)->latest()->firstOrCreate(
            ['name' => 'default'],
            ['tax_rate' => 11.00, 'platform_rate' => 10.00, 'is_active' => true]
        );
    }
}
