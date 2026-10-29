<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'nama',
        'jabatan',
        'fingerprint_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fingerprint_id' => 'integer',
        ];
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
