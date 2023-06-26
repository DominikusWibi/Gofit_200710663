<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Promo;

class PromoController extends Controller
{
    ///tampilkan promo
    public function index(){
        $promo = promo::all();
        return response()->json([
            'success' => true,
            'message' => 'Tampilkan semua Promo',
            'data' => $promo,
        ], 202);
    }
}
