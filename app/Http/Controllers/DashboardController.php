<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->join('seminar', 'sesi_seminar.seminar_id', '=', 'seminar.id')
            ->where('seminar.is_active', 'Yes');

        // Hitung peserta Approved
        $approvedCount = (clone $query)
            ->where('pendaftaran.status', 'Approved')
            ->count();

        // Hitung peserta Rejected
        $rejectedCount = (clone $query)
            ->where('pendaftaran.status', 'Rejected')
            ->count();

        // Hitung peserta Waiting
        $waitingCount = (clone $query)
            ->where('pendaftaran.status', 'Waiting')
            ->count();

        // Hitung total penjualan tiket (Rupiah)
        $totalPenjualan = (clone $query)
            ->where('pendaftaran.status', 'Approved')
            ->sum('sesi_seminar.harga_tiket');

        // =========================
        // Hitung persentase kehadiran
        // =========================

        $now = Carbon::now();
        // Get semua peserta yang approved dan sesi seminar setelah hari ini
        $totalPeserta = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('pendaftaran.status', 'Approved')
            ->where('sesi_seminar.tanggal_pelaksanaan', '<', $now)
            ->count();

        $totalPresensi = DB::table('presensi')
            ->join('pendaftaran', 'presensi.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('pendaftaran.status', 'Approved')
            ->where('sesi_seminar.tanggal_pelaksanaan', '<', $now)
            ->count();

        // Hitung persentase
        $persentaseKehadiran = 0;
        if ($totalPeserta > 0) {
            $persentaseKehadiran = ($totalPresensi / $totalPeserta) * 100;
        }

        return view('dashboard', [
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'waitingCount' => $waitingCount,
            'totalPenjualan' => $totalPenjualan,
            'persentaseKehadiran' => $persentaseKehadiran,
        ]);
    }
}
