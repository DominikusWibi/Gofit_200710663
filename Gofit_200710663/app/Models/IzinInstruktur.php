<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinInstruktur extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_jadwal_umum',
        'id_instruktur_berhalangan',
        'id_instruktur_pengganti',
        'izin',
        'keterangan',
        'konfirmasi',
    ];
}
