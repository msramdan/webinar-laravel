<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
use App\Http\Requests\Seminars\{StoreSeminarRequest, UpdateSeminarRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use App\Generators\Services\ImageService;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class SeminarController extends Controller implements HasMiddleware
{
    public function __construct(public ImageService $imageService, public string $lampiranPath = '')
    {
        $this->lampiranPath = storage_path('app/public/uploads/lampirans/');

    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            // 'auth',

            // TODO: uncomment this code if you are using spatie permission
            // new Middleware('permission:seminar view', only: ['index', 'show']),
            // new Middleware('permission:seminar create', only: ['create', 'store']),
            // new Middleware('permission:seminar edit', only: ['edit', 'update']),
            // new Middleware('permission:seminar delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $seminars = Seminar::query();

            return Datatables::of($seminars)
                ->addColumn('deskripsi', function($row) {
                        return str($row->deskripsi)->limit(100);
                    })

                ->addColumn('lampiran', function ($seminar) {
                    if (!$seminar->lampiran) return 'https://via.placeholder.com/350?text=No+Image+Avaiable';

                    return asset('storage/uploads/lampirans/' . $seminar->lampiran);
                })

                ->addColumn('action', 'seminar.include.action')
                ->toJson();
        }

        return view('seminar.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('seminar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeminarRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['lampiran'] = $this->imageService->upload(name: 'lampiran', path: $this->lampiranPath);

        Seminar::create($validated);

        return to_route('seminar.index')->with('success', __('The seminar was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Seminar $seminar): View
    {
        return view('seminar.show', compact('seminar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seminar $seminar): View
    {
        return view('seminar.edit', compact('seminar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeminarRequest $request, Seminar $seminar): RedirectResponse
    {
        $validated = $request->validated();

        $validated['lampiran'] = $this->imageService->upload(name: 'lampiran', path: $this->lampiranPath, defaultImage: $seminar?->lampiran);

        $seminar->update($validated);

        return to_route('seminar.index')->with('success', __('The seminar was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seminar $seminar): RedirectResponse
    {
        try {
            $lampiran = $seminar->lampiran;

            $seminar->delete();

            $this->imageService->delete(image: $this->lampiranPath . $lampiran);

            return to_route('seminar.index')->with('success', __('The seminar was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('seminar.index')->with('error', __("The seminar can't be deleted because it's related to another table."));
        }
    }
}
