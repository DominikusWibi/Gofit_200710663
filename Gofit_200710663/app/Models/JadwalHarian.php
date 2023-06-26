<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalHarian extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_jadwal_umum',
        'id_instruktur',
        'tanggal',
        'status',
    ];
}

