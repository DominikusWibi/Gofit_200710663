<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    //tampilkan daftar kelas
    public function index(Request $request){
        $kelas = kelas::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Kelas',
            'data' => $kelas
        ], 200);
    }
}
