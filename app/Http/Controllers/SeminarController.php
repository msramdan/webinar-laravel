<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
use App\Http\Requests\Seminars\{StoreSeminarRequest, UpdateSeminarRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use App\Generators\Services\ImageService;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeminarController extends Controller implements HasMiddleware
{
    public function __construct(public ImageService $imageService, public string $lampiranPath = '')
    {
        $this->lampiranPath = storage_path('app/public/uploads/lampirans/');
        $this->sertifikatTemplatePath = storage_path('app/public/uploads/sertifikat_templates/');
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:seminar view', only: ['index', 'show']),
            new Middleware('permission:seminar create', only: ['create', 'store']),
            new Middleware('permission:seminar edit', only: ['edit', 'update']),
            new Middleware('permission:seminar delete', only: ['destroy']),
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
                ->addIndexColumn()
                ->addColumn('pembicara', function ($row) {
                    $editPembicara = route('seminar.pembicara.show', ['id' => $row->id]);
                    return '
                    <div class="text-center">
                        <a href="' . $editPembicara . '" class="btn btn-sm btn-warning"
                           style="width: 140px; background: #ffc107; border-color: #ffc107;"
                           data-toggle="tooltip" data-placement="left" title="Atur Responden">
                             Atur Pembicara
                        </a>
                    </div>';
                })

                ->addColumn('sponsor', function ($row) {
                    $editSponsor = route('seminar.sponsor.show', ['id' => $row->id]);
                    return '
                    <div class="text-center">
                        <a href="' . $editSponsor . '" class="btn btn-sm btn-warning"
                           style="width: 140px; background: #ffc107; border-color: #ffc107;"
                           data-toggle="tooltip" data-placement="left" title="Atur Responden">
                             Atur Sponsor
                        </a>
                    </div>';
                })

                ->addColumn('sesi', function ($row) {
                    $editSesi = route('seminar.sesi.show', ['id' => $row->id]);
                    return '
                    <div class="text-center">
                        <a href="' . $editSesi . '" class="btn btn-sm btn-warning"
                           style="width: 140px; background: #ffc107; border-color: #ffc107;"
                           data-toggle="tooltip" data-placement="left" title="Atur Responden">
                             Atur Sesi
                        </a>
                    </div>';
                })
                ->addColumn('deskripsi', function ($row) {
                    return str($row->deskripsi)->limit(100);
                })

                ->addColumn('lampiran', function ($seminar) {
                    if (!$seminar->lampiran) return 'https://via.placeholder.com/350?text=No+Image+Avaiable';

                    return asset('storage/uploads/lampirans/' . $seminar->lampiran);
                })

                ->addColumn('action', 'seminar.include.action')
                ->rawColumns(['pembicara', 'sponsor', 'sesi', 'action'])
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

        if ($request->hasFile('template_sertifikat')) {
            // Gunakan nama unik atau nama asli + timestamp
            $fileName = time() . '_' . $request->file('template_sertifikat')->getClientOriginalName();
            // Simpan file menggunakan Storage facade ke path yang ditentukan
            $request->file('template_sertifikat')->storeAs('public/uploads/sertifikat_templates', $fileName);
            // Simpan nama file ke database
            $validated['template_sertifikat'] = $fileName;
        } else {
            $validated['template_sertifikat'] = null;
        }
        Seminar::create($validated);

        return to_route('seminar.index')->with('success', __('The seminar was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Seminar $seminar): View
    {
        $seminarData = DB::table('seminar')
            ->where('id', $seminar->id)
            ->first();

        $sponsors = DB::table('sponsor')
            ->where('seminar_id', $seminar->id)
            ->get();

        $pembicaras = DB::table('pembicara')
            ->where('seminar_id', $seminar->id)
            ->get();

        $sesiSeminars = DB::table('sesi_seminar')
            ->where('seminar_id', $seminar->id)
            ->get();

        return view('seminar.show', [
            'seminar' => $seminarData,
            'sponsors' => $sponsors,
            'pembicaras' => $pembicaras,
            'sesiSeminars' => $sesiSeminars,
        ]);
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

        if ($request->hasFile('template_sertifikat')) {
            // 1. Hapus file lama jika ada
            if ($seminar->template_sertifikat) {
                Storage::delete('public/uploads/sertifikat_templates/' . $seminar->template_sertifikat);
            }
            // 2. Upload file baru
            $fileName = time() . '_' . $request->file('template_sertifikat')->getClientOriginalName();
            $request->file('template_sertifikat')->storeAs('public/uploads/sertifikat_templates', $fileName);
            // 3. Simpan nama file baru ke database
            $validated['template_sertifikat'] = $fileName;
        } else {
            // Jika tidak ada file baru diupload, JANGAN ubah nama file di database
            // Hapus key dari array validated agar tidak mengupdate jadi null
            unset($validated['template_sertifikat']);
        }
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
            $templateSertifikat = $seminar->template_sertifikat;

            $seminar->delete();

            $this->imageService->delete(image: $this->lampiranPath . $lampiran);
            if ($templateSertifikat) {
                Storage::delete('public/uploads/sertifikat_templates/' . $templateSertifikat);
            }

            return to_route('seminar.index')->with('success', __('The seminar was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('seminar.index')->with('error', __("The seminar can't be deleted because it's related to another table."));
        }
    }


    public function showPembicara($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $pembicara = DB::table('pembicara')
                ->where('seminar_id', $id)
                ->get();

            return DataTables::of($pembicara)
                ->addIndexColumn()
                ->toJson();
        }

        $seminar = DB::table('seminar')
            ->where('id', $id)
            ->first();

        return view('seminar.pembicara', compact('seminar'));
    }

    public function showSponsor($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $sponsor = DB::table('sponsor')
                ->where('seminar_id', $id)
                ->get();

            return DataTables::of($sponsor)
                ->addIndexColumn()
                ->toJson();
        }

        $seminar = DB::table('seminar')
            ->where('id', $id)
            ->first();

        return view('seminar.sponsor', compact('seminar'));
    }

    public function showSesi($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $sesi_seminar = DB::table('sesi_seminar')
                ->where('seminar_id', $id)
                ->get();

            return DataTables::of($sesi_seminar)
                ->addIndexColumn()
                ->toJson();
        }

        $seminar = DB::table('seminar')
            ->where('id', $id)
            ->first();

        return view('seminar.sesi', compact('seminar'));
    }
}
