<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
     /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $pegawai = Pegawai::get();
        //render view with posts
        return view('pegawai.index', compact('pegawai'));
    }
}
