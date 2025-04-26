<?php

namespace App\Http\Controllers\PanelPeserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class DashboardPesertaController extends Controller
{

    public function seminarSaya()
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;

        $pendaftaran = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->join('seminar', 'sesi_seminar.seminar_id', '=', 'seminar.id')
            ->where('pendaftaran.peserta_id', $pesertaId)
            ->select(
                'pendaftaran.id',
                'pendaftaran.token', // untuk QR code
                'seminar.nama_seminar',
                'sesi_seminar.nama_sesi',
                'pendaftaran.status',
                'pendaftaran.tanggal_pengajuan',
                'sesi_seminar.tanggal_pelaksanaan',
                'sesi_seminar.link_gmeet'
            )
            ->orderBy('pendaftaran.id', 'desc') // <<< ini tambahan nya
            ->get();

        return view('panel-peserta.seminar_saya', compact('pendaftaran'));
    }



    public function semuaSeminar()
    {
        $seminars = DB::table('seminar')
            ->leftJoin('sesi_seminar', 'seminar.id', '=', 'sesi_seminar.seminar_id')
            ->where('seminar.is_active', 'Yes')
            ->select('seminar.id', 'seminar.nama_seminar', DB::raw('COUNT(sesi_seminar.id) as jumlah_sesi'))
            ->groupBy('seminar.id', 'seminar.nama_seminar')
            ->orderBy('seminar.id', 'desc') // <<< tambahan disini
            ->get();

        return view('panel-peserta.semua_seminar', compact('seminars'));
    }



    public function downloadQRCode($id)
    {
        $registration = DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->where('pendaftaran.id', $id)
            ->select('pendaftaran.*', 'peserta.nama as peserta_nama')
            ->first();

        if (!$registration || $registration->status != 'Approved') {
            abort(404, 'QR Code hanya tersedia untuk peserta dengan status Approved');
        }

        $qrData = json_encode([
            'sesi_id' => $registration->sesi_id,
            'peserta_id' => $registration->peserta_id,
            'token' => $registration->token,
            'timestamp' => now()->toDateTimeString()
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrData);

        $filename = 'QR-' . Str::slug($registration->peserta_nama) . '-' . $registration->id . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function lihatSesi($id)
    {
        // Retrieve the seminar using the query builder
        $seminar = DB::table('seminar')->where('id', $id)->first();

        if (!$seminar) {
            // Handle case where seminar is not found
            return redirect()->route('panel-peserta.semuaSeminar')->with('error', 'Seminar not found.');
        }

        // Retrieve all sessions for the seminar using the query builder
        $sesi = DB::table('sesi_seminar')
            ->leftJoin('pendaftaran', function ($join) {
                $join->on('sesi_seminar.id', '=', 'pendaftaran.sesi_id')
                    ->where('pendaftaran.status', '=', 'Approved');
            })
            ->select(
                'sesi_seminar.*',
                DB::raw('sesi_seminar.kuota - COUNT(pendaftaran.id) as sisa_kuota'), // Hitung sisa kuota
                DB::raw('COUNT(pendaftaran.id) as filled_kuota') // Kalau mau tahu juga berapa yang sudah isi
            )
            ->where('sesi_seminar.seminar_id', $id)
            ->groupBy('sesi_seminar.id')
            ->get();

        // Pass seminar data and its sessions to the view
        return view('panel-peserta.lihat_sesi', compact('seminar', 'sesi'));
    }

    public function register(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'sesi_id' => 'required|exists:sesi_seminar,id',
        ]);

        $pesertaId = Auth::guard('panel-peserta')->user()->id;

        // Get session info
        $sessionData = DB::table('sesi_seminar')
            ->where('id', $request->sesi_id)
            ->first(['seminar_id', 'kuota']);

        if (!$sessionData) {
            return response()->json(['error' => 'Sesi tidak ditemukan'], 404);
        }

        // Check existing registrations in the seminar
        $existingRegistrations = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('pendaftaran.peserta_id', $pesertaId)
            ->where('sesi_seminar.seminar_id', $sessionData->seminar_id)
            ->select('pendaftaran.status', 'pendaftaran.sesi_id', 'sesi_seminar.nama_sesi')
            ->get();

        // Rule 1: If any Approved → reject
        $approvedRegistration = $existingRegistrations->firstWhere('status', 'Approved');
        if ($approvedRegistration) {
            return response()->json([
                'error' => 'Anda sudah memiliki pendaftaran yang di-APPROVE di sesi "' . $approvedRegistration->nama_sesi . '".'
            ], 400);
        }

        // Rule 2: If any Waiting → reject
        $waitingRegistration = $existingRegistrations->firstWhere('status', 'Waiting');
        if ($waitingRegistration) {
            return response()->json([
                'error' => 'Anda sudah memiliki pendaftaran yang masih WAITING di sesi "' . $waitingRegistration->nama_sesi . '".'
            ], 400);
        }
        // Rule 3: Already registered in same session → reject
        $alreadyRegisteredInSession = $existingRegistrations->contains('sesi_id', $request->sesi_id);

        if ($alreadyRegisteredInSession) {
            return response()->json(['error' => 'Anda sudah terdaftar di sesi ini.'], 400);
        }

        // Rule 4: (Kuota) if intended to be Approved now
        $approvedCount = DB::table('pendaftaran')
            ->where('sesi_id', $request->sesi_id)
            ->where('status', 'Approved')
            ->count();

        if ($approvedCount >= $sessionData->kuota) {
            return response()->json(['error' => 'Kuota sesi sudah penuh.'], 400);
        }

        // Insert registration
        DB::table('pendaftaran')->insert([
            'sesi_id' => $request->sesi_id,
            'peserta_id' => $pesertaId,
            'status' => 'Waiting', // Always set Waiting during registration
            'tanggal_pengajuan' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Pendaftaran berhasil. Menunggu konfirmasi dari admin.']);
    }
}
