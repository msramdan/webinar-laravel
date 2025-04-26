<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;


class PendaftaranController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pendaftaran view', only: ['index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $seminars = DB::table('seminar')
                ->leftJoin('sesi_seminar', 'seminar.id', '=', 'sesi_seminar.seminar_id')
                ->select('seminar.id', 'seminar.nama_seminar', DB::raw('COUNT(sesi_seminar.id) as jumlah_sesi'))
                ->groupBy('seminar.id', 'seminar.nama_seminar');

            return DataTables::of($seminars)
                ->addColumn('jumlah_sesi', function ($row) {
                    return $row->jumlah_sesi . ' Sesi';
                })

                ->addColumn('action', function ($row) {
                    return view('pendaftaran.include.action', compact('row'))->render();
                })
                ->toJson();
        }

        return view('pendaftaran.index');
    }

    public function pesertaSesi($id): View
    {
        return view('pendaftaran.peserta_sesi');
    }

}
