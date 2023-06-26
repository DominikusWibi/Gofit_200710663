<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class Member extends Model
{
    use HasFactory;
    use HasApiTokens,HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    /**
* fillable
*
* @var array
*/
protected $fillable = [
'nama',
'tanggal_lahir',
'telepon',
'gender',
'alamat',
'tanggal_bergabung',
'password',
'deactived_membership_at',
'deposit_reguler',
'deposit_kelas_paket',
'deactived_deposit_kelas_paket',
'id_kelas_deposit_kelas_paket',
]; 
protected $hidden = [
    'password',
    'remember_token'
];
protected $dates = ['deleted_at'];
}
