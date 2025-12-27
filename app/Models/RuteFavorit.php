<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuteFavorit extends Model
{
    protected $fillable = ['user_id','nama_label','asal_nama','tujuan_nama','asal_coords','tujuan_coords'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
