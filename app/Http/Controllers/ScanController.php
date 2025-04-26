<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

class ScanController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:scan view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $scans = DB::table('sesi_seminar')
                ->join('seminar', 'sesi_seminar.seminar_id', '=', 'seminar.id')
                ->select(
                    'sesi_seminar.id',
                    'sesi_seminar.nama_sesi',
                    'seminar.nama_seminar',
                    'sesi_seminar.tanggal_pelaksanaan'
                );

            return DataTables::of($scans)
                ->addColumn('action', 'scan.include.action')
                ->toJson();
        }

        return view('scan.index');
    }


    public function show($id): View
    {
        return view('scan.show');
    }
}
