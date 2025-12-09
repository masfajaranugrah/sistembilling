<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;

class UserList extends Controller
{
    public function index()
    {
        return view('content.apps.karyawan.karyawan-list');
    }
}
