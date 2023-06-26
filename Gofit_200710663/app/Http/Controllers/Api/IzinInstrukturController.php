<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\IzinInstruktur as izinInstruktur;
use App\Models\JadwalHarians as jadwalHarian;

class IzinInstrukturController extends Controller
{
     //cek apakah instruktur ada jadwal yg tabrak
     public function cekJadwalInstruktur(Request $request){
        $jadwalUmum = DB::table('jadwal_umums')
            ->where('id_instruktur', $request->id_instruktur_penganti)
            ->where('hari', Carbon::parse($request->tanggal_izin)->format('l'))
            ->where('jam_kelas', '>' ,Carbon::parse($request->jam_kelas)->subHour()->format('H:i'))
            ->where('jam_kelas', '<' ,Carbon::parse($request->jam_kelas)->addHour()->format('H:i'))
            ->first();
        $izinInstruktur = DB::table('izin_instrukturs')
            ->leftJoin('jadwal_umums', 'izin_instrukturs.jadwal_umum_id', '=', 'jadwal_umums.id')
            ->where('izin_instrukturs.id_instruktur_penganti', $request->id_instruktur_penganti)
            ->where('jadwal_umums.hari', Carbon::parse($request->tanggal_izin)->format('l'))
            ->where('jadwal_umums.jam_kelas', '>' ,Carbon::parse($request->jam_kelas)->subHour()->format('H:i'))
            ->where('jadwal_umums.jam_kelas', '<' ,Carbon::parse($request->jam_kelas)->addHour()->format('H:i'))
            ->Where('izin_instrukturs.konfirmasi', '!=' ,1)
            ->first();
        if(!is_null($jadwalUmum) || !is_null($izinInstruktur)){
            return true;
        }else{
            return false;
        }
    }
        //tampilkan daftar izin (MO)
    public function index(Request $request){
        $izinInstruktur = DB::table('izin_instrukturs')
            ->Join('jadwal_umums', 'izin_instrukturs.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->Join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->Join('instrukturs as instrukturs_pengganti', 'izin_instrukturs.id_instruktur_pengganti', '=', 'instrukturs_pengganti.id')
            ->Join('instrukturs as instrukturs_berhalangan', 'izin_instrukturs.id_instruktur_berhalangan', '=', 'instrukturs_berhalangan.id')
            ->select('izin_instrukturs.*','kelas.nama as nama_kelas', DB::raw("TIME_FORMAT(jadwal_umums.jam_kelas, '%H:%i') as jam_mulai"), 'instrukturs_pengganti.nama as instruktur_pengganti', 'instrukturs_berhalangan.nama as instruktur_berhalangan')
            ->orderBy('izin_instrukturs.created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'message' => 'Daftar izin instruktur',
            'data' => $izinInstruktur
        ], 202);
    }
    public function indexPending(Request $request){
        $izinInstruktur = DB::table('izin_instrukturs')
            ->Join('jadwal_umums', 'izin_instrukturs.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->Join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->Join('instrukturs as instrukturs_pengganti', 'izin_instrukturs.id_instruktur_pengganti', '=', 'instrukturs_pengganti.id')
            ->Join('instrukturs as instrukturs_berhalangan', 'izin_instrukturs.id_instruktur_berhalangan', '=', 'instrukturs_berhalangan.id')
            ->where('izin_instrukturs.konfirmasi',0)
            ->select('izin_instrukturs.*','kelas.nama as nama_kelas', DB::raw("TIME_FORMAT(jadwal_umums.jam_kelas, '%H:%i') as jam_mulai"), 'instrukturs_pengganti.nama as instruktur_pengganti', 'instrukturs_berhalangan.nama as instruktur_berhalangan')
            ->orderBy('izin_instrukturs.created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'message' => 'Daftar izin instruktur',
            'data' => $izinInstruktur
        ], 202);
    }
    //Konfimasi izin instruktur (MO)
    public function updateVerifIzin( $id){
        $izinInstruktur = izinInstruktur::find($id);
        if(is_null($izinInstruktur)){
            return response()->json([
                'success' => false,
                'message' => 'Izin Instruktur tidak ditemukan',
                'data' => null
            ], 405);
        }
        $izinInstruktur->konfirmasi = 1;

        // if($request->konfirmasi == 2){
        //     $jadwalHarian = jadwalHarian::where('id_jadwal_umum', $izinInstruktur->id_jadwal_umum)
        //     ->where('tanggal', $izinInstruktur->izin)
        //     ->first();
            
        //     if($jadwalHarian != null){
        //         $jadwalHarian->status_id = 2;
        //         $jadwalHarian->save();
        //     }
        // }
        
        if($izinInstruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan izin',
                'data' => $izinInstruktur
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan izin',
                'data' => null
            ], 405);
        }
    }
    //tambahkan izin instruktur (Instruktur)
    public function add(Request $request){
        $instruktur = instruktur::find($request->user()->id);
        if(is_null($instruktur)){
            return response()->json([
                'success' => false,
                'message' => 'Hanya instruktur yang mendapat akses',
                'data' => null
            ], 405);
        }
        $Validator = Validator::make($request->all(), [
            'id_jadwal_umum' => 'required',
            'id_instruktur_penganti' => 'required',
            'izin' => 'required|date',
            'keterangan' => 'required',
        ]);
        if($Validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $Validator->errors(),
                'data' => null
            ], 405);
        }
        if(self::cekJadwalInstruktur($request)){
            return response()->json([
                'success' => false,
                'message' => [
                    'id_instruktur' => ['Pengganti tidak bisa'],
                ],
                'data' => null
            ], 405);
        }
        $izinInstruktur = new izinInstruktur();
        $izinInstruktur->id_jadwal_umum = $request->id_jadwal_umum;
        $izinInstruktur->id_instruktur_penganti = $request->id_instruktur_penganti;
        $izinInstruktur->id_instruktur_berhalangan = $request->user()->id;
        $izinInstruktur->izin = $request->izin;
        $izinInstruktur->keterangan = $request->keterangan;
        if($izinInstruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $izinInstruktur
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Tidak valid',
                'data' => null
            ], 405);
        }
    }
    //tampilkan daftar izin (instruktur)
    public function show(Request $request){
        $instruktur = instruktur::find($request->user()->id);
        if(is_null($instruktur)){
            return response()->json([
                'success' => false,
                'message' => 'Akses gagal',
                'data' => null
            ], 401);
        }
        $izinInstruktur = DB::table('izin_instrukturs')
            ->leftJoin('jadwal_umums', 'izin_instrukturs.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->leftJoin('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->leftJoin('instrukturs as instrukturs_penganti', 'izin_instrukturs.instruktur_penganti_id', '=', 'instrukturs.id')
            ->leftJoin('instrukturs as instrukturs_berhalangan', 'izin_instrukturs.id_instruktur_berhalangan', '=', 'instrukturs_pengaju.id')
            ->select('kelas.nama', 'instrukturs_penganti.nama', 'instrukturs_berhalangan.nama','izin_instrukturs.izin', 'izin_instrukturs.konfirmasi')
            ->where('izin_instrukturs.id_instruktur_halangan', $request->user()->id)
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar izin instruktur',
            'data' => $izinInstruktur
        ], 202);
    }
}

    

