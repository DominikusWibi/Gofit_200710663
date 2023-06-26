<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Instruktur;

class InstrukturController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function show()
    {
        //render view with posts
        $instruktur = Instruktur::all();
        return response()->json([
             'success' => true,
             'message' => 'Daftar Instruktur',
             'data' => $instruktur
        ], 202);
    }
        
        //Register untuk  instruktur (hanya  dapat diakses oleh admin)
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:instrukturs,nama|unique:members,nama|unique:pegawais,nama',
            'alamat' => 'required|string',
            'tanggal_kelahiran' => 'required|date|date_format:Y-m-d',
            'telepon' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 405);
        }
        $instruktur = new Instruktur();
        $instruktur->nama = $request->nama;
        $instruktur->alamat = $request->alamat;
        $instruktur->tanggal_kelahiran = $request->tanggal_kelahiran;
        $instruktur->telepon = $request->telepon;
        $instruktur->password = bcrypt($request->password);

        if($instruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Instruktur telah didaftarkan',
                'data' => $instruktur
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal terdaftar',
                'data' => null
            ], 405);

    }
}
//ubah data instruktur (hanya  dapat diakses oleh admin)
public function update(Request $request, $id){
    $instruktur = Instruktur::find($id);
    if(is_null($instruktur)){
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada',
            'data' => null
        ], 405);
    }
    $validator = Validator::make($request->all(), [
        'nama' => 'required|unique:instrukturs,nama,'.$instruktur->id.'|unique:members,nama|unique:pegawais,nama',
        'alamat' => 'required|string',
        'tanggal_kelahiran' => 'required|date|date_format:Y-m-d',
        'telepon' => 'required|string', 
    ]);

    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => null
        ], 405);
    }

    $instruktur->nama = $request->nama;
    $instruktur->alamat = $request->alamat;
    $instruktur->tanggal_kelahiran = $request->tanggal_kelahiran;
    $instruktur->telepon = $request->telepon;

    if($instruktur->save()){
        return response()->json([
            'success' => true,
            'message' => 'Sudah diubah',
            'data' => $instruktur
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa diubah',
            'data' => null
        ], 405);
    }
}
//Hapus data instruktur (hanya  dapat diakses oleh admin)
public function delete(Request $request, $id){
    if(!self::cekAdmin($request)){
        return response()->json([
            'success' => false,
            'message' => 'Hanya admin yang memiliki akses',
            'data' => null
        ], 405);
    }
    $instruktur = Instruktur::find($id);
    if(is_null($instruktur)){
        return response()->json([
            'success' => false,
            'message' => 'Data tidak dapat ditemukan',
            'data' => null
        ], 405);
    }
    if($instruktur->delete()){
        return response()->json([
            'success' => true,
            'message' => 'Data telah terhapus',
            'data' => $instruktur
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa dihapus',
            'data' => null
        ], 405);
    }
}
//Tampil data instruktur (hanya  dapat diakses oleh admin)
public function find(Request $request){
    if(!self::cekAdmin($request)){
        return response()->json([
            'success' => false,
            'message' => 'Hanya Admin saja',
            'data' => null
        ], 405);
    }
    return response()->json([
        'success' => true,
        'message' => 'Data Instruktur',
        'data' => $instruktur
    ], 202);
}
}
