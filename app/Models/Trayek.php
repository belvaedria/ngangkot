<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trayek extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'daftar_jalan' => 'array',
        'tampil_di_menu' => 'boolean'
    ];
}
