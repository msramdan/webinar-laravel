<div class="row mb-2">
    {{-- Nama Seminar --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama-seminar">{{ __('Nama Seminar') }}</label>
            <input type="text" name="nama_seminar" id="nama-seminar"
                class="form-control @error('nama_seminar') is-invalid @enderror"
                value="{{ isset($seminar) ? $seminar->nama_seminar : old('nama_seminar') }}"
                placeholder="{{ __('Nama Seminar') }}" required />
            @error('nama_seminar')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    {{-- Deskripsi --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="deskripsi">{{ __('Deskripsi') }}</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                placeholder="{{ __('Deskripsi') }}" required>{{ isset($seminar) ? $seminar->deskripsi : old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    {{-- Lampiran --}}
    @isset($seminar)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5 text-center">
                    @if (!$seminar->lampiran)
                        <img src="https://via.placeholder.com/350?text=No+Image+Avaiable" alt="Lampiran"
                            class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/lampirans/' . $seminar->lampiran) }}" alt="Lampiran"
                            class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="lampiran">{{ __('Lampiran') }}</label>
                        <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror"
                            id="lampiran">
                        @error('lampiran')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                        <div id="lampiran-help-block" class="form-text">
                            {{ __('Leave the lampiran blank if you don`t want to change it.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="lampiran">{{ __('Lampiran') }}</label>
                <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror"
                    id="lampiran" required>
                @error('lampiran')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset
    {{-- Is Active --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="is-active">{{ __('Is Active') }}</label>
            <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" id="is-active"
                class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select is active') }} --</option>
                <option value="Yes"
                    {{ isset($seminar) && $seminar->is_active == 'Yes' ? 'selected' : (old('is_active') == 'Yes' ? 'selected' : '') }}>
                    Yes</option>
                <option value="No"
                    {{ isset($seminar) && $seminar->is_active == 'No' ? 'selected' : (old('is_active') == 'No' ? 'selected' : '') }}>
                    No</option>
            </select>
            @error('is_active')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    {{-- === AWAL PENAMBAHAN === --}}
    {{-- Show Sertifikat --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="show_sertifikat">{{ __('Tampilkan Sertifikat?') }}</label>
            <select class="form-select @error('show_sertifikat') is-invalid @enderror" name="show_sertifikat"
                id="show_sertifikat" required>
                <option value="" selected disabled>-- {{ __('Pilih Opsi') }} --</option>
                {{-- Default ke 'No' jika baru membuat atau value lama 'No' --}}
                <option value="Yes"
                    {{ isset($seminar) && $seminar->show_sertifikat == 'Yes' ? 'selected' : (old('show_sertifikat') == 'Yes' ? 'selected' : '') }}>
                    Yes</option>
                <option value="No"
                    {{ (isset($seminar) && $seminar->show_sertifikat == 'No') || !isset($seminar) ? 'selected' : (old('show_sertifikat') == 'No' ? 'selected' : '') }}>
                    No</option>
            </select>
            @error('show_sertifikat')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    {{-- Template Sertifikat --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="template_sertifikat">{{ __('Upload Template Sertifikat (Gambar)') }}</label>
            <input type="file" name="template_sertifikat" id="template_sertifikat"
                class="form-control @error('template_sertifikat') is-invalid @enderror"
                accept="image/png, image/jpeg, image/jpg">
            {{-- Tambahkan validasi accept --}}
            @error('template_sertifikat')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <div id="template-help-block" class="form-text">
                {{ __('Format: JPG, PNG. Kosongkan jika tidak ingin mengubah.') }}
            </div>
        </div>
        {{-- Tampilkan preview jika sedang edit dan ada template --}}
        @isset($seminar)
            @if ($seminar->template_sertifikat)
                <div class="mt-2">
                    <p>Template saat ini:</p>
                    <img src="{{ asset('storage/uploads/sertifikat_templates/' . $seminar->template_sertifikat) }}"
                        alt="Template Sertifikat" class="img-thumbnail" style="max-width: 200px; height: auto;">
                </div>
            @else
                <p class="text-muted mt-2">Belum ada template yang diunggah.</p>
            @endif
        @endisset
    </div>
    {{-- === AKHIR PENAMBAHAN === --}}

</div>
