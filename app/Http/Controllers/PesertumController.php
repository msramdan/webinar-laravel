<?php

namespace App\Http\Controllers;

use App\Models\Pesertum;
use App\Http\Requests\Pesertas\{StorePesertumRequest, UpdatePesertumRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class PesertumController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            // 'auth',

            // TODO: uncomment this code if you are using spatie permission
            // new Middleware('permission:pesertum view', only: ['index', 'show']),
            // new Middleware('permission:pesertum create', only: ['create', 'store']),
            // new Middleware('permission:pesertum edit', only: ['edit', 'update']),
            // new Middleware('permission:pesertum delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $pesertas = Pesertum::query();

            return DataTables::of($pesertas)
                ->addColumn('alamat', function($row) {
                        return str($row->alamat)->limit(100);
                    })
				->addColumn('action', 'pesertas.include.action')
                ->toJson();
        }

        return view('pesertas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pesertas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePesertumRequest $request): RedirectResponse
    {
        $validated = $request->validated();
		$validated['password'] = bcrypt($request->password);

        Pesertum::create($validated);

        return to_route('pesertas.index')->with('success', __('The pesertum was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesertum $pesertum): View
    {
        return view('pesertas.show', compact('pesertum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pesertum $pesertum): View
    {
        return view('pesertas.edit', compact('pesertum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePesertumRequest $request, Pesertum $pesertum): RedirectResponse
    {
        $validated = $request->validated();

        if (!$request->password) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($request->password);
        }

        $pesertum->update($validated);

        return to_route('pesertas.index')->with('success', __('The pesertum was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pesertum $pesertum): RedirectResponse
    {
        try {
            $pesertum->delete();

            return to_route('pesertas.index')->with('success', __('The pesertum was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('pesertas.index')->with('error', __("The pesertum can't be deleted because it's related to another table."));
        }
    }
}
