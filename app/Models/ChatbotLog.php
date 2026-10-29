<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $table = 'chatbot_logs';

    protected $fillable = [
        'nomor_whatsapp',
        'pesan_pengguna',
        'respons_chatbot',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    /**
     * Sesuai NF-03: privasi nomor WhatsApp pelanggan disamarkan pada tampilan admin.
     * Contoh: 081234567890 -> 0812****7890
     */
    public function getMaskedNomorAttribute(): string
    {
        $nomor = (string) $this->nomor_whatsapp;
        $len = strlen($nomor);
        if ($len <= 6) {
            return $nomor;
        }

        $prefix = substr($nomor, 0, 4);
        $suffix = substr($nomor, -4);
        $stars = str_repeat('*', max(3, $len - 8));

        return $prefix.$stars.$suffix;
    }
}
