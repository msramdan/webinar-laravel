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
                    // return view('laporan-presensi.include.action', compact('row'))->render();
                    return '<a href="' . route('laporan.rekap.presensi', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat</a>';
                })
                ->toJson();
        }

        return view('laporan-presensi.index');
    }

    public function rekapPresensi($id, Request $request)
    {
        $sessions = DB::table('sesi_seminar')
            ->where('seminar_id', $id)
            ->orderBy('tanggal_pelaksanaan')
            ->get();

        $selectedSession = $request->query('sesi_id', 'all');

        $query = DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('sesi_seminar.seminar_id', $id);

        // Apply session filter if not 'all'
        if ($selectedSession !== 'all') {
            $query->where('sesi_seminar.id', $selectedSession);
        }

        return view('laporan-presensi.rekap_presensi', compact('selectedSession', 'sessions'));

    }

    public function rekapfetchData(Request $request)
    {
        $sesiId = $request->input('id');
        $seminar_id = $request->input('seminar_id');

        $cekPresensi = DB::table('presensi')
            ->join('pendaftaran', 'pendaftaran.id', '=', 'presensi.pendaftaran_id')
            ->pluck('pendaftaran.peserta_id') 
            ->toArray();

        $query = DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->join('sesi_seminar as ss', 'pendaftaran.sesi_id', '=', 'ss.id')
            ->join('presensi', 'presensi.pendaftaran_id', '=', 'pendaftaran.peserta_id')
            ->where('ss.seminar_id', $seminar_id)
            ->select('pendaftaran.*', 'peserta.*', 'ss.*', 'ss.created_at as sesi_created_at');

        // if($sesiId !== 'all') {
        //     $query->where('ss.id', $sesiId);
        // }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no', function($row) {
                static $index = 1; 
                return $index++;
            })
            ->addColumn('nama_peserta', function ($row) {
                return $row->nama;
            })
            ->addColumn('sesi', function ($row) {
                return $row->nama_sesi;
            })
            ->addColumn('tanggal_pendaftaran', function ($row) {
                return $row->sesi_created_at;
            })
            ->addColumn('presensi', function ($row) use ($cekPresensi) {
                if (in_array($row->peserta_id, $cekPresensi)) {
                    return '<span class="badge bg-success">Hadir</span>';
                } else {
                    return '<span class="badge bg-danger">Tidak Hadir</span>';
                }
            })
            ->rawColumns(['presensi'])
            ->make(true);

    }
}
