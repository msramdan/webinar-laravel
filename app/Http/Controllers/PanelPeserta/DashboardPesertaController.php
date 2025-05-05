<?php

namespace App\Http\Controllers\PanelPeserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class DashboardPesertaController extends Controller
{

    public function seminarSaya()
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;
        $namaPeserta = Auth::guard('panel-peserta')->user()->nama; // Ambil nama peserta

        $pendaftaran = DB::table('pendaftaran as p') // Alias tabel pendaftaran
            ->join('sesi_seminar as ss', 'p.sesi_id', '=', 'ss.id') // Alias sesi_seminar
            ->join('seminar as sm', 'ss.seminar_id', '=', 'sm.id') // Alias seminar
            ->leftJoin('presensi as pr', 'p.id', '=', 'pr.pendaftaran_id') // Left join ke presensi
            ->where('p.peserta_id', $pesertaId)
            ->select(
                'p.id',
                'p.token', // Pastikan token ada jika diperlukan untuk QR
                'sm.nama_seminar',
                'ss.nama_sesi',
                'p.status',
                'p.tanggal_pengajuan',
                'ss.tanggal_pelaksanaan',
                'ss.tempat_seminar',
                'ss.link_gmeet',
                'sm.show_sertifikat',
                DB::raw('COUNT(pr.id) > 0 as sudah_presensi')
            )
            ->groupBy( // Group by semua kolom non-agregat
                'p.id',
                'p.token',
                'sm.nama_seminar',
                'ss.nama_sesi',
                'p.status',
                'p.tanggal_pengajuan',
                'ss.tanggal_pelaksanaan',
                'ss.tempat_seminar',
                'ss.link_gmeet',
                'sm.show_sertifikat'
            )
            ->orderBy('p.id', 'desc')
            ->get();

        // Tambahkan nama peserta ke setiap item pendaftaran untuk view
        $pendaftaran->each(function ($item) use ($namaPeserta) {
            $item->nama_peserta = $namaPeserta;
        });


        return view('panel-peserta.seminar_saya', compact('pendaftaran'));
    }

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

        $defaultTemplatePath = public_path('images/sertifikat-bg.png');

        $templateFilePath = $defaultTemplatePath; // default

        if (!empty($pendaftaran->template_sertifikat)) {
            $uploadedPath = storage_path('app/public/uploads/sertifikat_templates/' . $pendaftaran->template_sertifikat);

            if (file_exists($uploadedPath)) {
                $templateFilePath = $uploadedPath;
            }
        }

        $data = [
            'namaPeserta'   => $pendaftaran->nama_peserta,
            'pendaftaranId' => $pendaftaran->pendaftaran_id,
            'nomorSertifikat' => 'dsada',
            'templatePath' => $templateFilePath, // <- path lokal, bukan URL
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
        $pesertaId = Auth::guard('panel-peserta')->user()->id; // Validasi kepemilikan

        $registration = DB::table('pendaftaran as p')
            ->join('peserta as ps', 'p.peserta_id', '=', 'ps.id')
            ->join('sesi_seminar as ss', 'p.sesi_id', '=', 'ss.id') // Join sesi
            ->where('p.id', $id)
            ->where('p.peserta_id', $pesertaId) // Cek kepemilikan
            ->select('p.*', 'ps.nama as peserta_nama', 'ss.nama_sesi') // Ambil nama sesi
            ->first();

        if (!$registration) {
            abort(404, 'Pendaftaran tidak ditemukan atau Anda tidak berhak mengakses QR ini.');
        }

        if ($registration->status != 'Approved') {
            abort(403, 'QR Code hanya tersedia untuk pendaftaran yang sudah disetujui (Approved).');
        }

        // Gunakan data yang lebih relevan untuk QR presensi
        $qrData = json_encode([
            'pendaftaran_id' => $registration->id,
            'peserta_id' => $registration->peserta_id,
            'sesi_id' => $registration->sesi_id,
            'token' => $registration->token // Sertakan token jika masih digunakan untuk validasi scan
        ]);


        $renderer = new ImageRenderer(
            new RendererStyle(300), // Ukuran QR code
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrData);

        $filename = 'QR-' . Str::slug($registration->peserta_nama) . '-' . $registration->id . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"' // Paksa download
        ]);
    }

    // --- METHOD BARU UNTUK MENGAMBIL DATA QR VIA AJAX ---
    public function generateQrCodeData($id)
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id; // Validasi kepemilikan

        $registration = DB::table('pendaftaran as p')
            ->join('peserta as ps', 'p.peserta_id', '=', 'ps.id')
            ->join('sesi_seminar as ss', 'p.sesi_id', '=', 'ss.id') // Join sesi
            ->where('p.id', $id)
            ->where('p.peserta_id', $pesertaId) // Cek kepemilikan
            ->select('p.*', 'ps.nama as peserta_nama', 'ss.nama_sesi') // Ambil nama sesi
            ->first();

        // Validasi: Tidak ditemukan atau bukan milik user
        if (!$registration) {
            return response()->json(['error' => 'Pendaftaran tidak ditemukan atau Anda tidak berhak.'], 404);
        }

        // Validasi: Status belum Approved
        if ($registration->status != 'Approved') {
            return response()->json(['error' => 'QR Code hanya tersedia untuk pendaftaran yang sudah disetujui.'], 403);
        }

        // Data untuk di-encode dalam QR Code
        $qrData = json_encode([
            'pendaftaran_id' => $registration->id,
            'peserta_id' => $registration->peserta_id,
            'sesi_id' => $registration->sesi_id,
            'token' => $registration->token // Sertakan token jika masih digunakan untuk validasi scan
        ]);


        // Generate SVG QR Code
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(250), // Ukuran QR code (sesuaikan dengan modal)
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $svg = $writer->writeString($qrData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat QR Code.'], 500);
        }


        // Kembalikan data sebagai JSON
        return response()->json([
            'svg' => $svg,
            'nama_peserta' => $registration->peserta_nama,
            'nama_sesi' => $registration->nama_sesi,
            'download_url' => route('pendaftaran.qrcode.download.peserta', ['id' => $id]) // URL untuk tombol download
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

        // Cek apakah auto approve diaktifkan
        $autoApprove = env('AUTO_APPROVED_DAFTAR', false);

        // Jika auto approve dan kuota masih tersedia
        if ($autoApprove && $approvedCount < $sessionData->kuota) {
            // Langsung approve pendaftaran
            $pendaftaranId = DB::table('pendaftaran')->insertGetId([
                'sesi_id'           => $request->sesi_id,
                'peserta_id'        => $pesertaId,
                'status'            => 'Approved',
                'tanggal_pengajuan' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Generate nomor pendaftaran
            $tahun = date('Y');
            $nomorUrut = str_pad($pendaftaranId, 4, '0', STR_PAD_LEFT);
            $nomorPendaftaranOtomatis = 'P' . $tahun . '-' . $nomorUrut;

            DB::table('pendaftaran')
                ->where('id', $pendaftaranId)
                ->update(['nomor_pendaftaran' => $nomorPendaftaranOtomatis]);

            return response()->json(['message' => 'Pendaftaran berhasil dan langsung di-APPROVE.']);
        }
        // Jika kuota penuh
        elseif ($approvedCount >= $sessionData->kuota) {
            return response()->json(['error' => 'Kuota sesi sudah penuh.'], 400);
        }
        // Jika tidak auto approve atau kuota penuh tapi auto approve false
        else {
            // Insert dengan status Waiting
            $pendaftaranId = DB::table('pendaftaran')->insertGetId([
                'sesi_id'           => $request->sesi_id,
                'peserta_id'        => $pesertaId,
                'status'            => 'Waiting',
                'tanggal_pengajuan' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Generate nomor pendaftaran
            $tahun = date('Y');
            $nomorUrut = str_pad($pendaftaranId, 4, '0', STR_PAD_LEFT);
            $nomorPendaftaranOtomatis = 'P' . $tahun . '-' . $nomorUrut;

            DB::table('pendaftaran')
                ->where('id', $pendaftaranId)
                ->update(['nomor_pendaftaran' => $nomorPendaftaranOtomatis]);

            return response()->json(['message' => 'Pendaftaran berhasil. Menunggu konfirmasi dari admin.']);
        }
    }



    public function getProfile()
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;
        $peserta = DB::table('peserta')
            ->join('kampus', 'peserta.kampus_id', '=', 'kampus.id')
            ->select('peserta.*', 'kampus.nama_kampus')
            ->where('peserta.id', $pesertaId)
            ->first();

        $kampus = DB::table('kampus')->get();

        return view('panel-peserta.update_profile', compact('peserta', 'kampus'));
    }

    public function updateProfile(Request $request)
    {
        $pesertaId = Auth::guard('panel-peserta')->user()->id;
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'email' => 'required|email|unique:peserta,email,' . $pesertaId,
            'alamat' => 'required|string',
            'kampus_id' => 'required|exists:kampus,id',
            'password' => 'nullable|string|min:6|confirmed', // optional password
        ]);

        $data = [
            'nama' => $request->nama,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'kampus_id' => $request->kampus_id,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('peserta')
            ->where('id', $pesertaId)
            ->update($data);
        Alert::toast('Profil berhasil diperbarui.', 'success');
        return redirect()->back();
    }
}
