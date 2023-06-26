<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
return view('dashboard'); /* arahkan ke halaman dashboard */
});
//Route Resource
Route::resource('/instruktur', 
\App\Http\Controllers\Api\InstrukturController::class);

//Route Resource
Route::resource('/member', 
\App\Http\Controllers\Api\MemberController::class);

//Route Resource
Route::resource('/jadwalumum', 
\App\Http\Controllers\Api\JadwalUmumController::class);

//Route Resource
Route::resource('/kelas', 
\App\Http\Controllers\Api\KelasController::class);

//Route Resource
Route::resource('/pegawai', 
\App\Http\Controllers\Api\PegawaiController::class);

// Route Resource
//Route::resource('/jadwalharian', \App\Http\Controllers\Api\JadwalHarianController::class);