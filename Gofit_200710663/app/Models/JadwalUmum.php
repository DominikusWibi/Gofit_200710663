<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUmum extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
    * fillable
    *
    * @var array
    */
    protected $fillable = [
    'id_kelas',
    'id_instruktur',
    'jam_kelas',
    'hari',
    ];
}
