<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDF;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Illuminate\Support\Str;

class LaporanPresensiController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:laporan presensi view', only: ['index',]),
            new Middleware('permission:laporan presensi export', only: ['downloadPresensi', 'downloadPresensiExcel']),
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
                    $pdfUrl = route('laporan.presensi.download', ['sesi' => $row->id]);
                    $excelUrl = route('laporan.presensi.download.excel', ['sesi' => $row->id]);
                    $buttons .= '
                    <a href="' . $pdfUrl . '" class="btn btn-sm btn-danger me-1" title="Download PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="' . $excelUrl . '" class="btn btn-sm btn-success" title="Download Excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                ';
                }
                return $buttons;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function downloadPresensi($sesiId)
    {
        // Ambil semua peserta yang daftar ke sesi ini, apapun status presensinya
        $peserta = DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->leftJoin('presensi', function ($join) {
                $join->on('pendaftaran.id', '=', 'presensi.pendaftaran_id');
            })
            ->where('pendaftaran.sesi_id', $sesiId)
            ->select(
                'peserta.nama',
                'peserta.no_telepon',
                'peserta.email',
                'peserta.alamat',
                DB::raw('CASE WHEN presensi.id IS NULL THEN "Tidak Hadir" ELSE "Hadir" END as status_presensi'),
                'presensi.waktu_presensi'
            )
            ->orderBy('peserta.nama')
            ->get();

        $sesi = DB::table('sesi_seminar')
            ->where('id', $sesiId)
            ->first();

        $pdf = \PDF::loadView('laporan-presensi.laporan', compact('peserta', 'sesi'));

        return $pdf->stream('laporan-presensi-' . $sesi->nama_sesi . '.pdf');
    }

    public function downloadPresensiExcel($sesiId)
    {
        $sesi = DB::table('sesi_seminar')
            ->where('id', $sesiId)
            ->first();

        if (!$sesi) {
            abort(404, 'Sesi seminar tidak ditemukan');
        }

        // Nama file Excel
        $fileName = 'laporan-presensi-' . Str::slug($sesi->nama_sesi) . '.xlsx';

        // Download menggunakan class Export (pastikan App\Exports\PresensiExport sudah dibuat)
        return Excel::download(new PresensiExport($sesiId), $fileName);
    }
}
