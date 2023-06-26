<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class pegawai extends Model
{
    use HasFactory;
    use HasApiTokens, HasFactory;
    public $incrementing = false;
    /**
* fillable
*
* @var array
*/
protected $fillable = [
    'nama',
    'alamat',
    'telepon',
    'role',
    'tanggal_kelahiran',
    'password',
    ]; 
    protected $hidden = [
        'password',
        'remember_token'
    ];
}
