<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LaporanPresensiController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:laporan presensi view', only: ['index']),
        ];
    }


    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $seminars = DB::table('seminar')
                ->leftJoin('sesi_seminar', 'seminar.id', '=', 'sesi_seminar.seminar_id')
                ->select('seminar.id', 'seminar.nama_seminar', DB::raw('COUNT(sesi_seminar.id) as jumlah_sesi'))
                ->groupBy('seminar.id', 'seminar.nama_seminar');

            return DataTables::of($seminars)
                ->addIndexColumn()
                ->addColumn('jumlah_sesi', function ($row) {
                    return $row->jumlah_sesi . ' Sesi';
                })

                ->addColumn('action', function ($row) {
                    return view('laporan-presensi.include.action', compact('row'))->render();
                })
                ->toJson();
        }

        return view('laporan-presensi.index');
    }

    public function laporanSesiSeminar($seminarId)
    {
        $seminar = DB::table('seminar')->where('id', $seminarId)->first();
        return view('laporan-presensi.daftar_sesi', compact('seminar'));
    }

    public function getData($seminarId)
    {
        $sesi = DB::table('sesi_seminar')
            ->where('seminar_id', $seminarId)
            ->select(['id', 'nama_sesi', 'kuota', 'harga_tiket', 'tanggal_pelaksanaan', 'link_gmeet', 'created_at', 'tempat_seminar']);

        return DataTables::of($sesi)
            ->addIndexColumn()
            ->addColumn('harga', function ($row) {
                return formatRupiah($row->harga_tiket);;
            })
            ->addColumn('tanggal', function ($row) {
                return date('d M Y H:i', strtotime($row->tanggal_pelaksanaan));
            })
            ->addColumn('action', function ($row) {
                $buttons = '';
                if (auth()->user()->can('laporan presensi export')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-danger btn-download-presensi" data-id="' . $row->id . '">
                            <i class="fas fa-file-pdf"></i> Download
                        </button>
                    ';
                }
                return $buttons;
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
