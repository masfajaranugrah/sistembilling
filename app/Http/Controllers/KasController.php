<?php

namespace App\Http\Controllers;

class KasController extends Controller
{
    // masuk
    public function masuk()
    {
        return view('content.apps.kasmasukkeluar.masuk.masuk');
    }

    //   keluar
    public function keluar()
    {
        return view('content.apps.kasmasukkeluar.keluar.keluar');
    }
}
