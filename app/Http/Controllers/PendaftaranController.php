<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Http\Requests\Pendaftarans\{StorePendaftaranRequest, UpdatePendaftaranRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;


class PendaftaranController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pendaftaran view', only: ['index', 'show']),
            new Middleware('permission:pendaftaran create', only: ['create', 'store']),
            new Middleware('permission:pendaftaran edit', only: ['edit', 'update']),
            new Middleware('permission:pendaftaran delete', only: ['destroy']),
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pendaftaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendaftaranRequest $request): RedirectResponse
    {

        Pendaftaran::create($request->validated());

        return to_route('pendaftaran.index')->with('success', __('The pendaftaran was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['sesi:id', 'pesertum:id,nama']);

        return view('pendaftaran.show', compact('pendaftaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['sesi:id', 'pesertum:id,nama']);

        return view('pendaftaran.edit', compact('pendaftaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {

        $pendaftaran->update($request->validated());

        return to_route('pendaftaran.index')->with('success', __('The pendaftaran was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendaftaran $pendaftaran): RedirectResponse
    {
        try {
            $pendaftaran->delete();

            return to_route('pendaftaran.index')->with('success', __('The pendaftaran was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('pendaftaran.index')->with('error', __("The pendaftaran can't be deleted because it's related to another table."));
        }
    }
}
