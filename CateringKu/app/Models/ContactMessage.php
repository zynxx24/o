<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $primaryKey = 'message_id';

    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read', 'replied_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public static function getSubjectLabel(string $subject): string
    {
        $labels = [
            'general' => 'Pertanyaan Umum',
            'order' => 'Masalah Pesanan',
            'partnership' => 'Kerjasama Bisnis',
            'vendor' => 'Daftar Vendor',
            'feedback' => 'Saran & Masukan',
            'complaint' => 'Komplain',
        ];
        return $labels[$subject] ?? ucfirst($subject);
    }
}
