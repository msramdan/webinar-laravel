<?php

namespace App\Http\Controllers\PanelPeserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class DashboardPesertaController extends Controller
{

    public function seminarSaya()
    {
        return view('panel-peserta.seminar_saya');
    }

    public function semuaSeminar()
    {
        return view('panel-peserta.semua_seminar');
    }


}
