<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Http\Request;
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
        // Ambil data sesi beserta seminar menggunakan Query Builder
        $sesi = DB::table('sesi_seminar')
            ->join('seminar', 'sesi_seminar.seminar_id', '=', 'seminar.id')
            ->select('sesi_seminar.*', 'seminar.nama_seminar', 'seminar.deskripsi')
            ->where('sesi_seminar.id', $id)
            ->first();

        if (!$sesi) {
            abort(404, 'Sesi tidak ditemukan');
        }

        return view('scan.show', compact('sesi'));
    }

    public function prosesScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'sesi_id' => 'required|exists:sesi_seminar,id'
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);

            // Validasi struktur QR
            if (!isset($qrData['sesi_id']) || !isset($qrData['peserta_id']) || !isset($qrData['token'])) {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Format QR code tidak valid'
                ], 400);
            }

            // Validasi sesi yang discan sesuai dengan halaman
            if ($qrData['sesi_id'] != $request->sesi_id) {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'QR code tidak sesuai dengan sesi ini'
                ], 400);
            }

            // Cek data pendaftaran
            $pendaftaran = DB::table('pendaftaran')
                ->where('sesi_id', $qrData['sesi_id'])
                ->where('peserta_id', $qrData['peserta_id'])
                ->where('token', $qrData['token'])
                ->first();

            if (!$pendaftaran) {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Data pendaftaran tidak ditemukan'
                ], 404);
            }

            // Cek apakah sudah pernah absen
            $sudahAbsen = DB::table('presensi')
                ->join('pendaftaran', 'presensi.pendaftaran_id', '=', 'pendaftaran.id')
                ->where('pendaftaran.peserta_id', $qrData['peserta_id'])
                ->where('pendaftaran.sesi_id', $qrData['sesi_id'])
                ->exists();

            if ($sudahAbsen) {
                return response()->json([
                    'sukses' => false,
                    'pesan' => 'Peserta sudah melakukan absen untuk sesi ini'
                ], 400);
            }

            // Rekam absensi
            DB::table('presensi')->insert([
                'pendaftaran_id' => $pendaftaran->id,
                'waktu_presensi' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Ambil nama peserta untuk ditampilkan
            $peserta = DB::table('peserta')
                ->where('id', $qrData['peserta_id'])
                ->select('nama')
                ->first();

            return response()->json([
                'sukses' => true,
                'pesan' => 'Absen berhasil direkam',
                'peserta' => $peserta->nama ?? 'Peserta'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
