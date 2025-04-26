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
                'pendaftaran.token', // tambahkan token untuk QR code
                'seminar.nama_seminar',
                'sesi_seminar.nama_sesi',
                'pendaftaran.status',
                'pendaftaran.tanggal_pengajuan',
                'sesi_seminar.tanggal_pelaksanaan',
                'sesi_seminar.link_gmeet'
            )
            ->get();

        return view('panel-peserta.seminar_saya', compact('pendaftaran'));
    }


    public function semuaSeminar()
    {
        return view('panel-peserta.semua_seminar');
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


}


