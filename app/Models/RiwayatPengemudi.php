<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengemudi extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'path_history_json' => 'array', // Agar otomatis jadi array saat diambil
    ];
}
