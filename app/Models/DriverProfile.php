<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_sim',
        'foto_ktp',
        'foto_sim',
        'alamat_domisili',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
