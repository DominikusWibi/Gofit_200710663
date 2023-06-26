<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\member;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function show()
    {
        //render view with posts
        $member = Member::all();
        return response()->json([
             'success' => true,
             'message' => 'Daftar member',
             'data' => $member
        ], 202);
    }
        
        //Register untuk  member (hanya  dapat diakses oleh kasir)
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:instrukturs,nama|unique:members,nama|unique:pegawais,nama',
            'tanggal_lahir' => 'required|date|date_format:Y-m-d',
            'telepon' => 'required|string',
            'gender' => 'required|string',
            'alamat' => 'required|string',
            'tanggal_bergabung' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 405);
        }
        $member = new Member();
        $member->nama = $request->nama;
        $member->tanggal_lahir = $request->tanggal_lahir;
        $member->telepon = $request->telepon;
        $member->gender = $request->gender;
        $member->alamat = $request->alamat;
        $member->tanggal_bergabung = $request->tanggal_bergabung;
        $member->password = bcrypt($request->password);

        if($member->save()){
            return response()->json([
                'success' => true,
                'message' => 'Member telah didaftarkan',
                'data' => $member
            ], 202);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal terdaftar',
                'data' => null
            ], 405);

    }
}
//ubah data member (hanya  dapat diakses oleh kasir)
public function update(Request $request, $id){
    $member = Member::find($id);
    if(is_null($member)){
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada',
            'data' => null
        ], 405);
    }
    $validator = Validator::make($request->all(), [
        'nama' => 'required|unique:members,nama,'.$member->id.'|unique:instrukturs,nama|unique:pegawais,nama',
        'tanggal_lahir' => 'required|date|date_format:Y-m-d',
        'alamat' => 'required|string',
        'telepon' => 'required|string',
        'gender' => 'required|string',
        'alamat' => 'required|string', 
        'tanggal_bergabung' => 'required|string',
    ]);

    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => null
        ], 405);
    }

    $member->nama = $request->nama;
    $member->tanggal_lahir = $request->tanggal_lahir;
    $member->telepon = $request->telepon;
    $member->gender = $request->gender;
    $member->alamat = $request->alamat;
    $member->tanggal_bergabung = $request->tanggal_bergabung;

    if($member->save()){
        return response()->json([
            'success' => true,
            'message' => 'Sudah diubah',
            'data' => $member
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa diubah',
            'data' => null
        ], 405);
    }
}
//Hapus data member (hanya  dapat diakses oleh kasir)
public function delete(Request $request, $id){
    $member = Member::find($id);
    if(is_null($member)){
        return response()->json([
            'success' => false,
            'message' => 'Data tidak dapat ditemukan',
            'data' => null
        ], 405);
    }
    if($member->delete()){
        return response()->json([
            'success' => true,
            'message' => 'Data telah terhapus',
            'data' => $member
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa dihapus',
            'data' => null
        ], 405);
    }
}
//Tampil data member (hanya dapat diakses oleh kasir)
public function find(Request $request){
    if(!self::cekKasir($request)){
        return response()->json([
            'success' => false,
            'message' => 'Hanya Kasir',
            'data' => null
        ], 405);
    }

    return response()->json([
        'success' => true,
        'message' => 'Informasi Pribadi member',
        'data' => $member
    ], 202);
}
//Reset password member (hanya member)
public function resetPasswordMember(Request $request, $id){
    $member = member::where('id', $id)->first();
    $member -> password = bcrypt(Carbon::parse($member -> tanggal_kelahiran)->format('dmy'));
    if($member->save()){
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengganti sandi',
            'data' => $member
        ], 202);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengganti sandi',
            'data' => null
        ], 405);
    }
}

    public function indexMembershipExpired(Request $request){
      
        $member = DB::table('members')
       
        ->where('deleted_at', null)
        ->where('deactived_membership_at', '<=', Carbon::now())
        ->select('members.id','members.nama', 'members.alamat', 'members.tanggal_lahir', 'members.telepon', DB::raw('IFNULL(members.deactived_membership_at, "Belum Aktif") as deactived_membership_at'))
        ->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Member',
            'data' => $member
        ], 200);
    }
    public function indexDepositKelasExpired(Request $request){
        $member = DB::table('members')
        ->leftJoin('kelas', 'members.id_kelas_deposit_kelas_paket', '=', 'kelas.id')
        ->where('deleted_at', null)
        ->where('deactived_deposit_kelas_paket', '<', Carbon::now())
        ->select('members.id','members.nama', 'members.tanggal_lahir', 'members.telepon', 'members.gender', 'members.alamat', 'members.tanggal_bergabung',DB::raw('IFNULL(members.deactived_membership_at, "Tidak Aktif") as deactived_membership_at'), 'members.deposit_reguler', 'members.deposit_kelas_paket', DB::raw('IFNULL(members.deactived_deposit_kelas_paket, "Tidak Aktif") as deactived_deposit_kelas_paket'), DB::raw('IFNULL(kelas.nama , "-") as kelas_deposit_kelas_paket'))
        ->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Member',
            'data' => $member
        ], 200);
    }
    //mendeactive membership yg kadarluasa
    public function deactiveMember(){
        $member = member::where('deactived_membership_at', '<', Carbon::now())
            ->get();
        foreach($member as $m){
            $m->deactived_membership_at = null;
            $m->save();
        }
        return;
    }
    //mendeactive deposit kelas paket yg kadarluasa
    public function deactiveDepositKelasPaketMember(){
        $member = member::where('deactived_deposit_kelas_paket', '<', Carbon::now())
            ->get();
        foreach($member as $m){
            $m->deposit_kelas_paket = 0;
            $m->deactived_deposit_kelas_paket = null;
            $m->id_kelas_deposit_kelas_paket = null;
            $m->save();
        }
}

}
