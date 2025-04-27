<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Http\Requests\Kampuses\{StoreKampusRequest, UpdateKampusRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Http\Request;

class KampusController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:kampus view', only: ['index', 'show']),
            new Middleware('permission:kampus create', only: ['create', 'store']),
            new Middleware('permission:kampus edit', only: ['edit', 'update']),
            new Middleware('permission:kampus delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $kampuses = Kampus::query();

            return DataTables::of($kampuses)
                ->addColumn('action', 'kampus.include.action')
                ->toJson();
        }

        return view('kampus.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('kampus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKampusRequest $request): RedirectResponse
    {

        Kampus::create($request->validated());

        return to_route('kampus.index')->with('success', __('The kampu was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Kampus $kampus): View
    {
        return view('kampus.show', compact('kampus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $kampus = Kampus::findOrFail($id);
        return view('kampus.edit', compact('kampus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKampusRequest $request, $id): RedirectResponse
    {
        $kampus = Kampus::findOrFail($id);

        // Using validated data from the custom request class
        $kampus->update($request->validated());

        return to_route('kampus.index')->with('success', __('The kampus was updated successfully.'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $kampus = Kampus::findOrFail($id);
            $kampus->delete();

            return to_route('kampus.index')->with('success', __('The kampus was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('kampus.index')->with('error', __("The kampus can't be deleted because it's related to another table."));
        }
    }
}
