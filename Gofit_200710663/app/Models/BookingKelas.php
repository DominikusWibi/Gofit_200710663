<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_kelas extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_nota',
        'id_member',
        'id_jadwal_harian',
    ];
}
