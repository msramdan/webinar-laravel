<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class PendaftaranController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pendaftaran view', only: ['index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $seminars = DB::table('seminar')
                ->leftJoin('sesi_seminar', 'seminar.id', '=', 'sesi_seminar.seminar_id')
                ->select('seminar.id', 'seminar.nama_seminar', DB::raw('COUNT(sesi_seminar.id) as jumlah_sesi'))
                ->groupBy('seminar.id', 'seminar.nama_seminar');

            return DataTables::of($seminars)
                ->addColumn('jumlah_sesi', function ($row) {
                    return $row->jumlah_sesi . ' Sesi';
                })

                ->addColumn('action', function ($row) {
                    return view('pendaftaran.include.action', compact('row'))->render();
                })
                ->toJson();
        }

        return view('pendaftaran.index');
    }

    public function pesertaSesi($id): View
    {
        $sessions = DB::table('sesi_seminar')
            ->where('seminar_id', $id)
            ->orderBy('tanggal_pelaksanaan')
            ->get();

        // Get all participants for all sessions of this seminar
        $participants = DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('sesi_seminar.seminar_id', $id)
            ->select(
                'pendaftaran.id as pendaftaran_id',
                'pendaftaran.status',
                'pendaftaran.tanggal_pengajuan',
                'peserta.*',
                'sesi_seminar.nama_sesi',
                'sesi_seminar.id as sesi_id'
            )
            ->orderBy('pendaftaran.tanggal_pengajuan', 'desc')
            ->get();

        // Get all available peserta for dropdown
        $allPeserta = DB::table('peserta')
            ->orderBy('nama')
            ->get();

        return view('pendaftaran.peserta_sesi', compact('sessions', 'participants', 'id', 'allPeserta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'sesi_id' => 'required|exists:sesi_seminar,id',
            'status' => 'required|in:Waiting,Approved,Rejected'
        ]);

        // Get seminar ID from selected session
        $seminarId = DB::table('sesi_seminar')
            ->where('id', $request->sesi_id)
            ->value('seminar_id');

        // Check existing registrations in this seminar
        $existingRegistrations = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('pendaftaran.peserta_id', $request->peserta_id)
            ->where('sesi_seminar.seminar_id', $seminarId)
            ->select('pendaftaran.status')
            ->get();

        // Rule 1: Jika sudah ada Approved di salah satu sesi → TOLAK
        if ($existingRegistrations->contains('status', 'Approved')) {
            return redirect()->back()
                ->with('error', 'Peserta sudah memiliki status APPROVED di sesi lain pada seminar ini');
        }

        // Rule 2: Jika sudah ada Waiting di salah satu sesi → TOLAK
        if ($existingRegistrations->contains('status', 'Waiting')) {
            return redirect()->back()
                ->with('error', 'Peserta sudah memiliki pendaftaran WAITING di sesi lain');
        }

        // Rule 3: Jika sudah terdaftar di sesi yang sama → TOLAK
        $existingInSession = DB::table('pendaftaran')
            ->where('peserta_id', $request->peserta_id)
            ->where('sesi_id', $request->sesi_id)
            ->exists();

        if ($existingInSession) {
            return redirect()->back()
                ->with('error', 'Peserta sudah terdaftar pada sesi ini');
        }

        // Rule 4: Jika semua status Rejected → BOLEH DAFTAR LAGI
        DB::table('pendaftaran')->insert([
            'sesi_id' => $request->sesi_id,
            'peserta_id' => $request->peserta_id,
            'status' => $request->status,
            'tanggal_pengajuan' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_seminar,id',
            'status' => 'required|in:Waiting,Approved,Rejected'
        ]);

        $currentRegistration = DB::table('pendaftaran')
            ->where('id', $id)
            ->first();

        $seminarId = DB::table('sesi_seminar')
            ->where('id', $request->sesi_id)
            ->value('seminar_id');

        // Untuk update, kita perlu mengecek registrasi lain SELAIN yang sedang diupdate
        $otherRegistrations = DB::table('pendaftaran')
            ->join('sesi_seminar', 'pendaftaran.sesi_id', '=', 'sesi_seminar.id')
            ->where('pendaftaran.peserta_id', $currentRegistration->peserta_id)
            ->where('sesi_seminar.seminar_id', $seminarId)
            ->where('pendaftaran.id', '!=', $id)
            ->select('pendaftaran.status')
            ->get();

        // Rule 1: Tidak boleh ada Approved lain jika mengubah ke Approved
        if ($request->status == 'Approved' && $otherRegistrations->contains('status', 'Approved')) {
            return redirect()->back()
                ->with('error', 'Sudah ada status APPROVED di sesi lain');
        }

        // Rule 2: Tidak boleh ada Waiting jika mengubah ke Waiting
        if ($request->status == 'Waiting' && $otherRegistrations->contains('status', 'Waiting')) {
            return redirect()->back()
                ->with('error', 'Sudah ada status WAITING di sesi lain');
        }

        DB::table('pendaftaran')
            ->where('id', $id)
            ->update([
                'sesi_id' => $request->sesi_id,
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('pendaftaran')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Pendaftaran berhasil dihapus');
    }
}
