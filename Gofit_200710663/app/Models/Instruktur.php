<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Instruktur extends Model
{
    use HasFactory;
    use HasApiTokens,HasFactory;
    /**
    * fillable
    *
    * @var array
    */
    protected $fillable = [
    'nama',
    'alamat',
    'tanggal_kelahiran',
    'telepon',
    'password',
    ];
} 