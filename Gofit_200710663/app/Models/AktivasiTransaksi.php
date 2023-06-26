<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivasiTransaksi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_pegawai',
        'id_kelas',
        'id_member',
        'id_jenis_transaksi',
        'id_promo',
        'nominal_transaksi',
        'total_transaksi',
    ];
}
