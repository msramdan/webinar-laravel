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
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardPesertaController extends Controller
{

    public function seminarSaya()
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;

        $pendaftaran = DB::table('pendaftaran as p') // Alias tabel pendaftaran
            ->join('sesi_seminar as ss', 'p.sesi_id', '=', 'ss.id') // Alias sesi_seminar
            ->join('seminar as sm', 'ss.seminar_id', '=', 'sm.id') // Alias seminar
            ->leftJoin('presensi as pr', 'p.id', '=', 'pr.pendaftaran_id') // Left join ke presensi
            ->where('p.peserta_id', $pesertaId)
            ->select(
                'p.id',
                'p.token',
                'sm.nama_seminar',
                'ss.nama_sesi',
                'p.status',
                'p.tanggal_pengajuan',
                'ss.tanggal_pelaksanaan',
                'ss.link_gmeet',
                'sm.show_sertifikat', // <-- Ambil kolom ini
                DB::raw('COUNT(pr.id) > 0 as sudah_presensi') // <-- Cek apakah ada record presensi
            )
            ->groupBy( // <-- Tambahkan groupBy untuk memastikan agregasi COUNT benar
                'p.id',
                'p.token',
                'sm.nama_seminar',
                'ss.nama_sesi',
                'p.status',
                'p.tanggal_pengajuan',
                'ss.tanggal_pelaksanaan',
                'ss.link_gmeet',
                'sm.show_sertifikat'
            )
            ->orderBy('p.id', 'desc')
            ->get();

        return view('panel-peserta.seminar_saya', compact('pendaftaran')); //
    } //

    public function downloadSertifikat($pendaftaranId)
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;

        $pendaftaran = DB::table('pendaftaran as p')
            ->join('peserta as ps', 'p.peserta_id', '=', 'ps.id')
            ->join('sesi_seminar as ss', 'p.sesi_id', '=', 'ss.id')
            ->join('seminar as sm', 'ss.seminar_id', '=', 'sm.id')
            ->leftJoin('presensi as pr', 'p.id', '=', 'pr.pendaftaran_id')
            ->where('p.id', $pendaftaranId)
            ->select(
                'p.id as pendaftaran_id',
                'p.peserta_id',
                'p.nomor_pendaftaran',
                'ps.nama as nama_peserta',
                'sm.nama_seminar',
                'sm.template_sertifikat',
                'sm.show_sertifikat',
                DB::raw('COUNT(pr.id) > 0 as sudah_presensi')
            )
            ->groupBy('p.id', 'p.peserta_id', 'p.nomor_pendaftaran', 'ps.nama', 'sm.nama_seminar', 'sm.show_sertifikat', 'sm.template_sertifikat')
            ->first();

        // 2. Validasi: Tidak ditemukan
        if (!$pendaftaran) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        // 3. Validasi: Kepemilikan
        if ($pendaftaran->peserta_id != $pesertaId) {
            abort(403, 'Anda tidak berhak mengakses sertifikat ini.');
        }

        // 4. Validasi: Seminar show_sertifikat != 'Yes'
        if ($pendaftaran->show_sertifikat !== 'Yes') {
            return redirect()->route('panel-peserta.seminarSaya')
                ->with('error', 'Sertifikat tidak tersedia untuk seminar ini.');
        }

        // 5. Validasi: Belum Presensi
        if (!$pendaftaran->sudah_presensi) {
            return redirect()->route('panel-peserta.seminarSaya')
                ->with('error', 'Anda harus melakukan presensi terlebih dahulu untuk mengunduh sertifikat.');
        }

        $templateImagePath = public_path('images/sertifikat-bg.png');
        if ($pendaftaran->template_sertifikat) {
            $uploadedPath = public_path('storage/uploads/sertifikat_templates/' . $pendaftaran->template_sertifikat);
            if (file_exists($uploadedPath)) {
                $templateImagePath = $uploadedPath;
            }
        }

        // 6. Siapkan data untuk view
        $data = [
            'namaPeserta'   => $pendaftaran->nama_peserta,
            'pendaftaranId' => $pendaftaran->pendaftaran_id,
            'nomorSertifikat' => $pendaftaran->nomor_pendaftaran,
            'templatePath' => $templateImagePath
        ];

        // 7. Generate PDF
        $pdf = Pdf::loadView('sertifikat.template', $data)
            ->setPaper('a4', 'landscape');

        $namaFile = 'Sertifikat-' . Str::slug($pendaftaran->nama_seminar) . '-' . Str::slug($pendaftaran->nama_peserta) . '.pdf';
        return $pdf->stream($namaFile);
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

        // Check existing registrations in the seminar (Validasi tetap sama)
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

        // Rule 4: (Kuota)
        $approvedCount = DB::table('pendaftaran')
            ->where('sesi_id', $request->sesi_id)
            ->where('status', 'Approved')
            ->count();

        if ($approvedCount >= $sessionData->kuota) {
            return response()->json(['error' => 'Kuota sesi sudah penuh.'], 400);
        }

        // --- MULAI MODIFIKASI UNTUK NOMOR PENDAFTARAN ---

        // 1. Insert data pendaftaran (tanpa nomor_pendaftaran) untuk mendapatkan ID
        $pendaftaranId = DB::table('pendaftaran')->insertGetId([
            'sesi_id'           => $request->sesi_id,
            'peserta_id'        => $pesertaId,
            'status'            => 'Waiting', // Status awal saat daftar
            'tanggal_pengajuan' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
            // 'token' akan diisi oleh admin saat Approved
        ]);

        // 2. Generate nomor pendaftaran
        $tahun = date('Y'); // Ambil tahun saat ini
        $nomorUrut = str_pad($pendaftaranId, 4, '0', STR_PAD_LEFT); // Format ID menjadi 4 digit
        $nomorPendaftaranOtomatis = 'P' . $tahun . '-' . $nomorUrut;

        // 3. Update record pendaftaran dengan nomor yang baru digenerate
        DB::table('pendaftaran')
            ->where('id', $pendaftaranId)
            ->update(['nomor_pendaftaran' => $nomorPendaftaranOtomatis]);

        // --- AKHIR MODIFIKASI ---

        return response()->json(['message' => 'Pendaftaran berhasil. Menunggu konfirmasi dari admin.']);
    }
}
