<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('loginMobile', 'App\Http\Controllers\Api\LoginController@loginMobile');
Route::post('loginWebsite','App\Http\Controllers\Api\LoginController@loginWeb');

//Jadwal Umum (hak akses MO)
Route::post('jadwalUmum/add', 'App\Http\Controllers\Api\JadwalUmumController@add');
Route::put('jadwalUmum/update/{id}', 'App\Http\Controllers\Api\JadwalUmumController@update');
Route::delete('jadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@delete');

 //instruktur (hak akses admin)
 Route::get('instruktur/tampil', 'App\Http\Controllers\Api\InstrukturController@show');
 Route::post('instruktur/register', 'App\Http\Controllers\Api\InstrukturController@register');
 Route::put('instruktur/update/{id}', 'App\Http\Controllers\Api\InstrukturController@update');
 Route::delete('instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@delete');

 //member (hak akses kasir)
 Route::get('member/index', 'App\Http\Controllers\Api\MemberController@show');
 Route::post('member/register', 'App\Http\Controllers\Api\MemberController@register');
 Route::put('member/update/{id}', 'App\Http\Controllers\Api\MemberController@update');
 Route::delete('member/{id}', 'App\Http\Controllers\Api\MemberController@delete');
 Route::put('member/resetpassword/{id}', 'App\Http\Controllers\Api\MemberController@resetPasswordMember');
 Route::get('member/indexExp', 'App\Http\Controllers\Api\MemberController@indexMembershipExpired');

 //jadwal harian (hak akses MO)
 Route::get('jadwalHarian/show', 'App\Http\Controllers\JadwalHariansController@index');
 Route::get('jadwalHarian/generate', 'App\Http\Controllers\JadwalHariansController@generateJadwalHarian');
 Route::put('jadwalHarian/libur/{id}', 'App\Http\Controllers\JadwalHariansController@updateLiburJadwalHarian');

 //promo
 Route::get('promo/show', 'App\Http\Controllers\Api\PromoController@index');

 //transaksi (hak akses kasir)
 Route::post('transaksi/aktivasi', 'App\Http\Controllers\Api\AktivasiTransaksiController@AktivasiTransaksi');
 Route::post('transaksi/depositReguler', 'App\Http\Controllers\Api\AktivasiTransaksiController@depositRegulerTransaksi');
 Route::post('transaksi/depositKelasPaket', 'App\Http\Controllers\Api\AktivasiTransaksiController@depositKelasPaketTransaksi');

 //Izin Instruktur (MO)
 Route::get('IzinInstruktur/index', 'App\Http\Controllers\Api\IzinInstrukturController@index');
 Route::get('IzinInstruktur/indexpending', 'App\Http\Controllers\Api\IzinInstrukturController@indexPending');
 Route::put('IzinInstruktur/verifikasi/{id}', 'App\Http\Controllers\Api\IzinInstrukturController@updateVerifIzin');

 // sistem
 Route::get('member/indexdepo', 'App\Http\Controllers\Api\MemberController@indexDepositKelasExpired');
 Route::put('member/resetdepo', 'App\Http\Controllers\Api\MemberController@deactiveDepositKelasPaketMember');
 Route::put('member/resetmember', 'App\Http\Controllers\Api\MemberController@deactiveMember');

 Route::get('bookingGym/tampil', 'App\Http\Controllers\Api\BookingGymController@show');
 Route::put('bookingGym/presensi/{id}', 'App\Http\Controllers\Api\BookingGymController@presensi');
 Route::get('laporan/tampil', 'App\Http\Controllers\Api\BookingGymController@laporanGym');
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
    
// });
