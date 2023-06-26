<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_promo',
        'jenis_pembelian',
        'bonus_promo',
        'promo_berlaku',
    ];
}
