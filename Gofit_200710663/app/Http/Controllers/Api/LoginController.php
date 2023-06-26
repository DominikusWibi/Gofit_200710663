<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\member;

class LoginController extends Controller
{
    public function loginWeb(Request $request){
        $loginData = $request->all();

        $validator = Validator::make($loginData, [
            'nama' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'tidak boleh kosong',
                'data' => $validator->errors()
            ], 405);
        }

        $user = pegawai::where('nama', $loginData['nama'])
        ->where('password',$loginData['password'])
        ->first();

        if(!is_null($user)){
            $token = $user->createToken('authToken')->plainTextToken;
            $tokenString = substr($token, strpos($token, '|') + 1);
            return response()->json([
                'success' => true,
                'message' => 'Dapat masuk',
                'data' => $tokenString,
            ], 203);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'gagal masuk',
                'data' => null
            ], 406);
        }
    }
    
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil',
            'data' => null
        ], 200);
    }

    public function loginMobile(Request $request){
        $loginData = $request->all();
        $user=null;
        $role=null;

        
    
        if(!is_null(member::where('nama', $loginData['nama'])->where('deleted_at', null)->first())){
            $role = 'member';
            $user = member::where('nama', $loginData['nama'])->first();
        } else if(!is_null(instruktur::where('nama', $loginData['nama'])->where('deleted_at', null)->first())){
            $role = 'instruktur';
            $user = instruktur::where('nama', $loginData['nama'])->first();
        } else if(!is_null(pegawai::where('nama', $loginData['nama'])->where('role_id', 1)->first())){
            $user = $pegawai;
            $user = pegawai::where('nama', $loginData['nama'])->where('role_id', 1)->first();
        }else{
            
            return response()->json([
                'success' => false,
                'message' => 'Login Gagal',
                'data' => null
            ], 401);
        }
    
        if(!is_null($user) && Hash::check($loginData['password'], $user->password)){
            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'data' => $user,
                'role' => $role,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Login Gagal',
                'data' => null
            ], 401);
        }
    }

    public function getUserMobile(Request $request){
        $pegawai = pegawai::find($request->user()->id);
        $instruktur = instruktur::find($request->user()->id);
        $member = member::find($request->user()->id);
        $user = null;
        $role = null;
    
        if(!is_null($member)){
            $user = $member;
            $role = 'member';
        } else if(!is_null($instruktur)){
            $user = $instruktur;
            $role = 'instruktur';
        } else if(!is_null($pegawai)){
            $user = $pegawai;
            $role = 'MO';
        }
        
        if(!is_null($user)){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan data user',
                'role' => $role,
                'data' => $user,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan data user',
                'data' => null
            ], 405);
        }
    }

    
}

