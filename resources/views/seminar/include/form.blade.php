<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama-seminar">{{ __('Nama Seminar') }}</label>
            <input type="text" name="nama_seminar" id="nama-seminar" class="form-control @error('nama_seminar') is-invalid @enderror" value="{{ isset($seminar) ? $seminar->nama_seminar : old('nama_seminar') }}" placeholder="{{ __('Nama Seminar') }}" required />
            @error('nama_seminar')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="deskripsi">{{ __('Deskripsi') }}</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="{{ __('Deskripsi') }}" required>{{ isset($seminar) ? $seminar->deskripsi : old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    @isset($seminar)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5 text-center">
                    @if (!$seminar->lampiran)
                        <img src="https://via.placeholder.com/350?text=No+Image+Avaiable" alt="Lampiran" class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/lampirans/' . $seminar->lampiran) }}" alt="Lampiran" class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="lampiran">{{ __('Lampiran') }}</label>
                        <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran">

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
                <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" required>

                @error('lampiran')
                   <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset
    <div class="col-md-6">
        <div class="form-group">
            <label for="is-active">{{ __('Is Active') }}</label>
            <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" id="is-active" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select is active') }} --</option>
                <option value="Yes" {{ isset($seminar) && $seminar->is_active == 'Yes' ? 'selected' : (old('is_active') == 'Yes' ? 'selected' : '') }}>Yes</option>
		<option value="No" {{ isset($seminar) && $seminar->is_active == 'No' ? 'selected' : (old('is_active') == 'No' ? 'selected' : '') }}>No</option>			
            </select>
            @error('is_active')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>