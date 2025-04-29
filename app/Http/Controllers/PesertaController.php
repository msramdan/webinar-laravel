<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Http\Requests\Pesertas\{StorePesertaRequest, UpdatePesertaRequest};
use App\Models\Kampus;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PesertaController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:peserta view', only: ['index', 'show']),
            new Middleware('permission:peserta create', only: ['create', 'store']),
            new Middleware('permission:peserta edit', only: ['edit', 'update']),
            new Middleware('permission:peserta delete', only: ['destroy']),
        ];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = DB::table('peserta')
                ->join('kampus', 'peserta.kampus_id', '=', 'kampus.id')
                ->select(
                    'peserta.id', // Pastikan ID Peserta disertakan
                    'peserta.nama',
                    'kampus.nama_kampus',
                    'peserta.no_telepon',
                    'peserta.email',
                    'peserta.alamat',
                    'peserta.is_verified' // Tambahkan kolom is_verified
                );

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('alamat', function ($row) {
                    return Str::limit($row->alamat, 50); // Batasi panjang alamat jika perlu
                })
                ->addColumn('is_verified', function ($row) { // Format kolom is_verified
                    if ($row->is_verified == 'Yes') {
                        return '<span class="badge bg-success">Terverifikasi</span>';
                    } else {
                        return '<span class="badge bg-danger">Belum</span>';
                    }
                })
                ->addColumn('action', 'peserta.include.action')
                ->rawColumns(['action', 'is_verified']) // Tambahkan is_verified ke rawColumns
                ->toJson();
        }

        return view('peserta.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kampus = Kampus::all(); // Get all Kampus records
        return view('peserta.create', compact('kampus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePesertaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($request->password);
        $validated['is_verified'] = $request->input('is_verified', 'No');

        Peserta::create($validated);

        return to_route('peserta.index')->with('success', __('Peserta berhasil dibuat.'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        // Ambil data peserta beserta nama kampusnya
        $peserta = DB::table('peserta')
            ->join('kampus', 'peserta.kampus_id', '=', 'kampus.id')
            ->select('peserta.*', 'kampus.nama_kampus')
            ->where('peserta.id', $id)
            ->firstOrFail();

        return view('peserta.show', compact('peserta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $peserta = Peserta::findOrFail($id);
        $kampus = Kampus::all(); // Get all Kampus records
        return view('peserta.edit', compact('peserta', 'kampus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePesertaRequest $request, $id): RedirectResponse
    {
        // Cari peserta berdasarkan id
        $peserta = Peserta::findOrFail($id);

        // Ambil semua data yang sudah tervalidasi
        $validated = $request->validated();

        // Tambahkan is_verified dari form saat update
        $validated['is_verified'] = $request->input('is_verified', $peserta->is_verified); // Ambil dari input, fallback ke nilai lama

        // Jika password tidak diisi, hapus key password agar tidak di‐update
        if (empty($request->password)) {
            unset($validated['password']);
        } else {
            // Encrypt password baru
            $validated['password'] = bcrypt($request->password);
        }

        // Update peserta
        $peserta->update($validated);

        return to_route('peserta.index')
            ->with('success', __('Data Peserta berhasil diperbarui.'));
    }

    public function destroy($id): RedirectResponse
    {
        try {
            // Cari peserta berdasarkan id
            $peserta = Peserta::findOrFail($id);

            // Hapus peserta
            $peserta->delete();

            return to_route('peserta.index')
                ->with('success', __('Peserta berhasil dihapus.'));
        } catch (\Exception $e) {
            // Cek apakah error disebabkan oleh constraint foreign key
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getMessage(), 'foreign key constraint fails')) {
                return to_route('peserta.index')
                    ->with('error', __("Peserta tidak dapat dihapus karena terkait dengan data lain (misal: pendaftaran seminar)."));
            }
            // Tampilkan pesan error umum jika bukan karena constraint
            return to_route('peserta.index')
                ->with('error', __("Terjadi kesalahan saat menghapus peserta."));
        }
    }
}
