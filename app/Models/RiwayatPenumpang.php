<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPenumpang extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'rute_hasil_json' => 'array',
    ];
}
