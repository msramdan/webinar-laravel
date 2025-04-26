<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PembicaraController extends Controller
{
    public function index($seminarId): View
    {
        $seminar = DB::table('seminar')->where('id', $seminarId)->first();
        return view('pembicara.index', compact('seminar'));
    }

    public function getData($seminarId)
    {
        $pembicara = DB::table('pembicara')
            ->where('seminar_id', $seminarId)
            ->select(['id', 'nama_pembicara', 'photo', 'latar_belakang']);

        return DataTables::of($pembicara)
            ->addIndexColumn()
            ->addColumn('photo', function ($row) {
                return $row->photo ? '<img src="' . asset('storage/uploads/pembicara/' . $row->photo) . '" width="80" class="img-thumbnail">' : '<i class="fas fa-user-circle fa-2x"></i>';
            })
            ->addColumn('action', function ($row) {
                $buttons = '';
                if (auth()->user()->can('pembicara edit')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                    ';
                }

                if (auth()->user()->can('pembicara delete')) {
                    $buttons .= '
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }

                return $buttons;
            })
            ->rawColumns(['photo', 'action'])
            ->toJson();
    }

    public function store(Request $request, $seminarId)
    {
        $request->validate([
            'nama_pembicara' => 'required|string|max:255',
            'latar_belakang' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads/pembicara', 'public');
            $photoPath = basename($photoPath);
        }

        DB::table('pembicara')->insert([
            'nama_pembicara' => $request->nama_pembicara,
            'latar_belakang' => $request->latar_belakang,
            'photo' => $photoPath,
            'seminar_id' => $seminarId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Pembicara berhasil ditambahkan']);
    }

    public function show($seminarId, $id)
    {
        $pembicara = DB::table('pembicara')->where('id', $id)->first();

        // Return proper JSON response
        return response()->json([
            'id' => $pembicara->id,
            'nama_pembicara' => $pembicara->nama_pembicara,
            'latar_belakang' => $pembicara->latar_belakang,
            'photo' => $pembicara->photo,
        ]);
    }

    public function update(Request $request, $seminarId, $id)
    {
        $request->validate([
            'nama_pembicara' => 'required|string|max:255',
            'latar_belakang' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pembicara = DB::table('pembicara')->where('id', $id)->first();

        $photoPath = $pembicara->photo;
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($photoPath) {
                Storage::disk('public')->delete('uploads/pembicara/' . $photoPath);
            }
            $photoPath = $request->file('photo')->store('uploads/pembicara', 'public');
            $photoPath = basename($photoPath);
        }

        DB::table('pembicara')
            ->where('id', $id)
            ->update([
                'nama_pembicara' => $request->nama_pembicara,
                'latar_belakang' => $request->latar_belakang,
                'photo' => $photoPath,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Pembicara berhasil diperbarui']);
    }

    public function destroy($seminarId, $id)
    {
        $pembicara = DB::table('pembicara')->where('id', $id)->first();

        if ($pembicara->photo) {
            Storage::disk('public')->delete('uploads/pembicara/' . $pembicara->photo);
        }

        DB::table('pembicara')->where('id', $id)->delete();

        return response()->json(['success' => 'Pembicara berhasil dihapus']);
    }
}
