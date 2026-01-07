<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Angkot extends Model
{
    use HasFactory;

    protected $fillable = [
        'plat_nomor',
        'trayek_id',
        'kode_trayek',
        'user_id',
        'is_active',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'lat_sekarang' => 'float',
        'lng_sekarang' => 'float',
        'is_active' => 'boolean',
    ];

    public function trayek()
    {
        // angkots.kode_trayek (string) -> trayeks.kode_trayek (string)
        return $this->belongsTo(Trayek::class, 'kode_trayek', 'kode_trayek');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatPengemudi::class);
    }
}
