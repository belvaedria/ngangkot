<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPenumpang extends Model
{
    protected $fillable = ['user_id','asal_nama','tujuan_nama','asal_coords','tujuan_coords','rute_hasil_json'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 
