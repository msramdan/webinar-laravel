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
                    'peserta.*',
                    'kampus.nama_kampus',
                );

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('alamat', function ($row) {
                    return Str::limit($row->alamat, 100);
                })
                ->addColumn('action', 'peserta.include.action')
                ->rawColumns(['action'])
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

        Peserta::create($validated);

        return to_route('peserta.index')->with('success', __('The Peserta was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $peserta = Peserta::where('id', $id)->firstOrFail();

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
    public function update(UpdatePesertaRequest $request, Peserta $peserta): RedirectResponse
    {
        $validated = $request->validated();

        if (!$request->password) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($request->password);
        }

        $peserta->update($validated);

        return to_route('peserta.index')->with('success', __('The Peserta was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peserta $peserta): RedirectResponse
    {
        try {
            $peserta->delete();

            return to_route('peserta.index')->with('success', __('The Peserta was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('peserta.index')->with('error', __("The Peserta can't be deleted because it's related to another table."));
        }
    }
}
