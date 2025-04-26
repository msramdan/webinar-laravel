<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Http\Requests\Pesertas\{StorePesertaRequest, UpdatePesertaRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

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

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $pesertas = Peserta::query();

            return DataTables::of($pesertas)
                ->addColumn('alamat', function ($row) {
                    return str($row->alamat)->limit(100);
                })
                ->addColumn('action', 'peserta.include.action')
                ->toJson();
        }

        return view('peserta.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('peserta.create');
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
    public function show(Peserta $peserta): View
    {
        return view('peserta.show', compact('peserta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peserta $peserta): View
    {
        return view('peserta.edit', compact('peserta'));
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
