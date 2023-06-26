<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingGym extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_nota',
        'nama_member',
        'slot_waktu',
        'tanggal_booking'
    ];
}
