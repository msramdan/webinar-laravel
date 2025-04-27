<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SesiSeminarController extends Controller
{
    public function index($seminarId)
    {
        $seminar = DB::table('seminar')->where('id', $seminarId)->first();
        return view('sesi.index', compact('seminar'));
    }

    public function getData($seminarId)
    {
        $sesi = DB::table('sesi_seminar')
            ->where('seminar_id', $seminarId)
            ->select(['id', 'nama_sesi', 'kuota', 'harga_tiket', 'tanggal_pelaksanaan', 'link_gmeet', 'created_at']);

        return DataTables::of($sesi)
            ->addIndexColumn()
            ->addColumn('harga', function ($row) {
                return 'Rp ' . number_format($row->harga_tiket, 0, ',', '.');
            })
            ->addColumn('tanggal', function ($row) {
                return date('d M Y H:i', strtotime($row->tanggal_pelaksanaan));
            })
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('sesi edit')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                }

                if (auth()->user()->can('sesi delete')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }

                return $buttons;
            })

            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request, $seminarId)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'harga_tiket' => 'required|numeric|min:0',
            'tanggal_pelaksanaan' => 'required|date',
            'link_gmeet' => 'nullable|url',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'tempat_seminar' => 'required|string|max:255',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('uploads/sesi', 'public');
            $lampiranPath = basename($lampiranPath);
        }

        DB::table('sesi_seminar')->insert([
            'nama_sesi' => $request->nama_sesi,
            'kuota' => $request->kuota,
            'harga_tiket' => $request->harga_tiket,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'link_gmeet' => $request->link_gmeet,
            'lampiran' => $lampiranPath,
            'seminar_id' => $seminarId,
            'tempat_seminar' => $request->tempat_seminar,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Sesi seminar berhasil ditambahkan']);
    }

    public function show($seminarId, $id)
    {
        $sesi = DB::table('sesi_seminar')->where('id', $id)->first();
        return response()->json([
            'id' => $sesi->id,
            'nama_sesi' => $sesi->nama_sesi,
            'kuota' => $sesi->kuota,
            'harga_tiket' => $sesi->harga_tiket,
            'tanggal_pelaksanaan' => $sesi->tanggal_pelaksanaan,
            'link_gmeet' => $sesi->link_gmeet,
            'lampiran' => $sesi->lampiran,
            'tempat_seminar' => $sesi->tempat_seminar
        ]);
    }

    public function update(Request $request, $seminarId, $id)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'harga_tiket' => 'required|numeric|min:0',
            'tanggal_pelaksanaan' => 'required|date',
            'link_gmeet' => 'nullable|url',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $sesi = DB::table('sesi_seminar')->where('id', $id)->first();
        $lampiranPath = $sesi->lampiran;

        if ($request->hasFile('lampiran')) {
            // Delete old file if exists
            if ($lampiranPath) {
                Storage::disk('public')->delete('uploads/sesi/'.$lampiranPath);
            }
            $lampiranPath = $request->file('lampiran')->store('uploads/sesi', 'public');
            $lampiranPath = basename($lampiranPath);
        }

        DB::table('sesi_seminar')
            ->where('id', $id)
            ->update([
                'nama_sesi' => $request->nama_sesi,
                'kuota' => $request->kuota,
                'harga_tiket' => $request->harga_tiket,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'link_gmeet' => $request->link_gmeet,
                'lampiran' => $lampiranPath,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Sesi seminar berhasil diperbarui']);
    }

    public function destroy($seminarId, $id)
    {
        $sesi = DB::table('sesi_seminar')->where('id', $id)->first();

        if ($sesi->lampiran) {
            Storage::disk('public')->delete('uploads/sesi/'.$sesi->lampiran);
        }

        DB::table('sesi_seminar')->where('id', $id)->delete();

        return response()->json(['success' => 'Sesi seminar berhasil dihapus']);
    }
}
