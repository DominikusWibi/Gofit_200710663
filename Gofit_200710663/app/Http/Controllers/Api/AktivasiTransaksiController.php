<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\member;
use App\Models\pegawai;
use App\Models\promo;


class AktivasiTransaksiController extends Controller
{
     //cek deposit kelas paket member
   public function cekKelasPaketMember($id){
    $member = member::find($id);
    if(is_null($member->deactived_deposit_kelas_paket)){
        return false;
    }else{
        return true;
    }
}
//create transaksi
public function createTransaksi(Request $request){
    $transaksi = new Transaksi;
    $transaksi->id_pegawai = $request->user()->id;
    $transaksi->id_member = $request->id_member;
    $transaksi->id_jenis_transaksi = $request->id_jenis_transaksi;
    $transaksi->save();
    
    $transaksi = Transaksi::where('pegawai_id', $request->user()->id)
        ->where('id_member', $request->id_member)
        ->where('id_jenis_transaksi', $request->id_jenis_transaksi)
        ->where('created_at', $transaksi->created_at)
        ->where('updated_at', $transaksi->updated_at)
        ->first();
    return $transaksi;
}
//create transaksi aktivasi
public function AktivasiTransaksi(Request $request){
    $validator = Validator::make($request->all(), [
        'id_member' => 'required',
        'id_jenis_transaksi' => 'required',
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => null
        ], 405);
    }
    $transaksi = self::createTransaksi($request);
    if(is_null($transaksi)){
        return response()->json([
            'success' => false,
            'message' => 'Transaksi gagal',
            'data' => null
        ], 405);
    }
    //menambah masa aktif member
    $member = member::find($request->member_id);
    if(is_null($member->deactived_membership_at)){
        $member->deactived_membership_at = Carbon::now()->addYear();
    }else{
        $member->deactived_membership_at = Carbon::parse($member->deactived_membership_at)->addYear();
    }
    if($member->save()){
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => [
                'no_nota' => $transaksi->id,
                'id_member' => $member->id,
                'nama' => $member->nama,
                'masa_aktif_member' => Carbon::parse($member->deactived_membership_at)->format('d/m/Y'),
                'created_at' => Carbon::parse($transaksi->created_at)->format('d/m/Y H:i'),
            ],
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Transaksi sukses',
            'data' => $transaksi,       
        ], 405);
    }
}
//create transaksi deposit reguler
public function depositRegulerTransaksi(Request $request){
    $validator = Validator::make($request->all(), [
        'id_member' => 'required',
        'id_jenis_transaksi' => 'required',
        'nominal' => 'required|integer|min:500000',
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => null
        ], 405);
    }

    $transaksi = self::createTransaksi($request);
    if(is_null($transaksi)){
        return response()->json([
            'success' => false,
            'message' => 'Transaksi gagal',
            'data' => null
        ], 405);
    }
    $AktivasiTransaksi = new AktivasiTransaksi;
    $AktivasiTransaksi->no_nota = $transaksi->id;       
    $AktivasiTransaksi->nominal = $request->nominal;

    //update deposit member
    $member = member::find($request->member_id);
    $sisa_deposit = $member->deposit_reguler; //simpan untuk dikembalikan ke client
    $member->deposit_reguler += $request->nominal;

    if(isset($request->id_promo)){
        $AktivasiTransaksi->id_promo = $request->id_promo;
        $promo = promo::find($request->id_promo);
        $member->deposit_reguler += $promo->bonus;
    }
     
    if($AktivasiTransaksi->save() && $member->save()){
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil',
            'data' => ['no_nota' => $transaksi->id,
                'id_member' => $member->id,
                'nama' => $member->nama,
                'sisa_deposit' => $sisa_deposit,
                'total_deposit' => $member->deposit_reguler,
                'created_at' => Carbon::parse($transaksi->created_at)->format('d/m/Y H:i'),
                ],
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Transaksi gagal',
            'data' => null
        ], 405);
    }
}
//create transaksi deposit kelas paket
public function depositKelasPaketTransaksi(Request $request){
    $validator = Validator::make($request->all(), [
        'id_member' => 'required',
        'id_jenis_transaksi' => 'required',
        'id_kelas' => 'required|integer',
        'nominal' => 'required|integer',
        'total' => 'required|integer',
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => null
        ], 405);
    }
    if(self::cekKelasPaketMember($request->member_id)){
        return response()->json([
            'success' => false,
            'message' => 'Paket sudah diambil member',
            'data' => null
        ], 402);
    }
    $transaksi = self::createTransaksi($request);
    if(is_null($transaksi)){
        return response()->json([
            'success' => false,
            'message' => 'Transaksi gagal',
            'data' => null
        ], 405);
    }
    $AktivasiTransaksi = new AktivasiTransaksi;
    $AktivasiTransaksi->no_nota = $transaksi->id;
    $AktivasiTransaksi->id_kelas = $request->id_kelas;
    $AktivasiTransaksi->nominal = $request->nominal;
    $AktivasiTransaksi->total = $request->total;

    //update data member
    $member = member::find($request->member_id);
    $member->deposit_kelas_paket += $request->nominal;
    $member->kelas_deposit_kelas_paket_id = $request->kelas_id;
    if($request->nominal < 10){
        $member->deactived_deposit_kelas_paket = Carbon::now()->addMonth();
    }else{
        $member->deactived_deposit_kelas_paket = Carbon::now()->addMonths(2);
    }

    if(isset($request->id_promo)){
        $AktivasiTransaksi->id_promo = $request->id_promo;
        $promo = promo::find($request->id_promo);
        $member->deposit_kelas_paket += $promo->bonus;
    }

    if($AktivasiTransaksi->save() && $member->save()){
        return response()->json([
            'success' => true,
            'message' => 'Transaksi Berhasil',
            'data' => [$transaksi,
                'no_nota' => $transaksi->id,
                'id_member' => $member->id,
                'nama_member' => $member->nama,
                'total_deposit' => $member->deposit_kelas_paket,
                'created_at' => Carbon::parse($transaksi->created_at)->format('d/m/Y H:i'),
                'masa_aktif_deposit_kelas_paket' => Carbon::parse($member->deactived_deposit_kelas_paket)->format('d/m/Y'),
            ],
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Transaksi gagal',
            'data' => null
        ], 405);
    }
}
}
