<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingGym;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class BookingGymController extends Controller
{
    public function show()
    {
        //render view with posts
        $instruktur = BookingGym::all();
        return response()->json([
             'success' => true,
             'message' => 'Daftar Instruktur',
             'data' => $instruktur
        ], 202);
    }

    public function presensi($id){
        
        
        $instruktur = BookingGym::find($id);
        $instruktur->waktu_presensi = Carbon::now();
       
        if($instruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Data telah presensi',
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
    public function laporanGym(){
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $daysInMonth = CarbonPeriod::create("{$currentYear}-{$currentMonth}-01", "1 day", "{$currentYear}-{$currentMonth}-31");

        $dataByDate = BookingGym::whereMonth('tanggal_booking', $currentMonth)
            ->whereYear('tanggal_booking', $currentYear)
            ->select('tanggal_booking', DB::raw('count(*) as jumlah_data'))
            ->groupBy('tanggal_booking')
            ->get()
            ->keyBy('tanggal_booking');

        $result = [];
        foreach ($daysInMonth as $date) {
            $formattedDate = $date->format('Y-m-d');
            $jumlahData = $dataByDate[$formattedDate]->jumlah_data ?? 0;
            $result[] = [
                'tanggal_booking' => $formattedDate,
                'jumlah_data' => $jumlahData,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'list laporan',
            'data' => $result,
        ], 200);
    }

}
