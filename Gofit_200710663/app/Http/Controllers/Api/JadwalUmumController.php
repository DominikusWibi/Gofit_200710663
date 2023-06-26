<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\jadwalUmum as jadwalUmum;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JadwalUmumController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $jadwalumum = JadwalUmum::get();

        //render view with posts
        $jadwalumum = JadwalUmum::all();
        return response()->json([
             'success' => true,
             'message' => 'Daftar Jadwal Umum',
             'data' => $jadwalumum
        ], 202);
    }
     //cek apakah jadwal instruktur sudah ada
     public function cekJadwalInstruktur(Request $request){
        $jadwalUmum = jadwalUmum::where('id_instruktur', $request->id_instruktur)
            ->where('hari', $request->hari)
            ->where('jam_kelas', '>' ,Carbon::parse($request->jam_kelas)->subHour()->format('H:i'))
            ->where('jam_kelas', '<' ,Carbon::parse($request->jam_kelas)->addHour()->format('H:i'))
            ->first();
        if(is_null($jadwalUmum)){
            return false;
        }else{
            return true;
        }
    }
    //Tambah jadwal umum (Untuk MO)
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'id_kelas' => 'required|integer',
            'id_instruktur' => 'required|integer',
            'jam_kelas' => 'required|date_format:H:i',
            'hari' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 405);
        }
        if(self::cekJadwalInstruktur($request)){
            return response()->json([
                'success' => false,
                'message' => [
                    'instruktur_id' => ['Instruktur masih ada kelas'],
                ],
                'data' => null
            ], 405);
        }
        $jadwalUmum = new jadwalUmum();
        $jadwalUmum->id_kelas = $request->id_kelas;
        $jadwalUmum->id_instruktur = $request->id_instruktur;
        $jadwalUmum->jam_kelas = $request->jam_kelas;
        $jadwalUmum->hari = $request->hari;
        if($jadwalUmum->save()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal telah ditambahkan',
                'data' => $jadwalUmum
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal gagal ditambahkan',
                'data' => null
            ], 405);
        }
    }
    //Ubah jadwal umum (MO)
    public function update(Request $request, $id){
       
        $validator = Validator::make($request->all(), [
            'id_kelas' => 'required|integer',
            'id_instruktur' => 'required|integer',
            'jam_kelas' => 'required|date_format:H:i',
            'hari' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 405);
        }
        if(self::cekJadwalInstruktur($request)){
            return response()->json([
                'success' => false,
                'message' => [
                    'instruktur_id' => ['Instruktur masih ada kelas'],
                ],
                'data' => null
            ], 405);
        }
        $jadwalUmum = jadwalUmum::find($id);
        if(is_null($jadwalUmum)){
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada',
                'data' => null
            ], 405);
        }
        $jadwalUmum->id_kelas = $request->id_kelas;
        $jadwalUmum->id_instruktur = $request->id_instruktur;
        $jadwalUmum->jam_kelas = $request->jam_kelas;
        $jadwalUmum->hari = $request->hari;
        if($jadwalUmum->save()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal telah ditambahkan',
                'data' => $jadwalUmum
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal gagal ditambahkan',
                'data' => null
            ], 405);
        }
    }
    //Hapus jadwal umum (MO)
    public function delete(Request $request, $id){
        $jadwalUmum = jadwalUmum::find($id);
        if(is_null($jadwalUmum)){
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal',
                'data' => null
            ], 405);
        }
        if($jadwalUmum->delete()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal dapat terhapus',
                'data' => $jadwalUmum
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak bisa dihapus',
                'data' => null
            ], 405);
        }
    }
}
