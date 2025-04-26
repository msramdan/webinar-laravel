<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SponsorController extends Controller
{
    public function index($seminarId)
    {
        $seminar = DB::table('seminar')->where('id', $seminarId)->first();
        return view('sponsor.index', compact('seminar'));
    }

    public function getData($seminarId)
    {
        $sponsor = DB::table('sponsor')
            ->where('seminar_id', $seminarId)
            ->select(['id', 'nama_sponsor', 'gambar', 'created_at']);

        return DataTables::of($sponsor)
            ->addIndexColumn()
            ->addColumn('gambar', function ($row) {
                return $row->gambar ?
                    '<img src="' . asset('storage/uploads/sponsor/' . $row->gambar) . '" width="80" class="img-thumbnail">' :
                    '<i class="fas fa-image fa-2x"></i>';
            })
            ->addColumn('action', function ($row) {
                $buttons = '';
                if (auth()->user()->can('sponsor edit')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                }
                if (auth()->user()->can('sponsor delete')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }

                return $buttons;
            })
            ->rawColumns(['gambar', 'action'])
            ->toJson();
    }

    public function store(Request $request, $seminarId)
    {
        $request->validate([
            'nama_sponsor' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $gambarPath = $request->file('gambar')->store('uploads/sponsor', 'public');
        $gambarPath = basename($gambarPath);

        DB::table('sponsor')->insert([
            'nama_sponsor' => $request->nama_sponsor,
            'gambar' => $gambarPath,
            'seminar_id' => $seminarId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Sponsor berhasil ditambahkan']);
    }

    public function show($seminarId, $id)
    {
        $sponsor = DB::table('sponsor')->where('id', $id)->first();

        if (!$sponsor) {
            return response()->json(['error' => 'Sponsor not found'], 404);
        }

        return response()->json([
            'id' => $sponsor->id,
            'nama_sponsor' => $sponsor->nama_sponsor,
            'gambar' => $sponsor->gambar,
        ]);
    }


    public function update(Request $request, $seminarId, $id)
    {
        $request->validate([
            'nama_sponsor' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $sponsor = DB::table('sponsor')->where('id', $id)->first();
        $gambarPath = $sponsor->gambar;

        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($gambarPath) {
                Storage::disk('public')->delete('uploads/sponsor/' . $gambarPath);
            }
            $gambarPath = $request->file('gambar')->store('uploads/sponsor', 'public');
            $gambarPath = basename($gambarPath);
        }

        DB::table('sponsor')
            ->where('id', $id)
            ->update([
                'nama_sponsor' => $request->nama_sponsor,
                'gambar' => $gambarPath,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Sponsor berhasil diperbarui']);
    }

    public function destroy($seminarId, $id)
    {
        $sponsor = DB::table('sponsor')->where('id', $id)->first();

        if ($sponsor->gambar) {
            Storage::disk('public')->delete('uploads/sponsor/' . $sponsor->gambar);
        }

        DB::table('sponsor')->where('id', $id)->delete();

        return response()->json(['success' => 'Sponsor berhasil dihapus']);
    }
}
