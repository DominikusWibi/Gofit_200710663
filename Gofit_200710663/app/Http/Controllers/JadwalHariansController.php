<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\JadwalUmum as JadwalUmum;
use App\Models\JadwalHarian as JadwalHarian;
use Illuminate\Support\Facades\DB;

class JadwalHariansController extends Controller
{
    //cek apakah sudah generate jadwal harian
    public function cekStatusGenerateAutomatic(){
        $jadwalHarian = jadwalHarian::where('tanggal', '>', Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d'))
            ->first();
        if(is_null($jadwalHarian)){
            return false;
        }else{
            return true;
        }
    }
    // generate jadwal harian
    public function generateJadwalHarian(Request $request){
        if(self::cekStatusGenerateAutomatic()){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal harian sudah di generate',
                'data' => null,
            ], 405);
        }
        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDay();
        $end_date =  Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(7);
        for($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $jadwalUmum = jadwalUmum::where('hari', Carbon::parse($date)->format('l'))->get();
            for($index = 0; $index < count($jadwalUmum); $index++){
                $jadwalHarian = new jadwalHarian;
                $jadwalHarian->id_jadwal_umum = $jadwalUmum[$index]->id;
                $jadwalHarian->tanggal = $date;
                
                // $izinInstruktur = izinInstruktur::where('jadwal_umum_id', $jadwalUmum[$index]->id)
                //     ->where('tanggal_izin', $date)
                //     ->where('is_confirmed', true)
                //     ->first();
                // if(!is_null($izinInstruktur)){
                //     $jadwalHarian->status = 2;
                // }
                $jadwalHarian->save();    
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil di generate',
            'data' => null
        ], 202);
    }
    // meliburkan jadwal harian
    public function updateLiburJadwalHarian(Request $request, $id){
        $jadwalHarian = jadwalHarian::find($id);
        if(is_null($jadwalHarian)){
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada',
                'data' => null
            ], 405);
        }
        $jadwalHarian->status= 'Libur';
        $jadwalHarian->save();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil diliburkan',
            'data' => null
        ], 202);
    }
      //tampilkan jadwal harian

      public function index(){
        $jadwalHarian = DB::table('jadwal_harians')
            ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
            ->select('jadwal_harians.id', 'jadwal_harians.tanggal', 'jadwal_umums.jam_kelas', 'kelas.nama as nama_kelas', 'instrukturs.nama as nama_instruktur', 'jadwal_harians.status','jadwal_umums.hari','jadwal_umums.jam_kelas')
            ->get();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil tampil',
                'data' => $jadwalHarian
            ], 202);
}

}
