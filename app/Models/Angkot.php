<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Angkot extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'lat_sekarang' => 'float',
        'lng_sekarang' => 'float',
        'is_active' => 'boolean',
    ];

    public function trayek()
    {
        return $this->belongsTo(Trayek::class);
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
