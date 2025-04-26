<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="sesi-id">{{ __('Sesi') }}</label>
            <select class="form-select @error('sesi_id') is-invalid @enderror" name="sesi_id" id="sesi-id" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select sesi') }} --</option>
                
                        @foreach ($sesis as $sesi)
                            <option value="{{ $sesi?->id }}" {{ isset($pendaftaran) && $pendaftaran?->sesi_id == $sesi?->id ? 'selected' : (old('sesi_id') == $sesi?->id ? 'selected' : '') }}>
                                {{ $sesi?->id }}
                            </option>
                        @endforeach
            </select>
            @error('sesi_id')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="peserta-id">{{ __('Pesertum') }}</label>
            <select class="form-select @error('peserta_id') is-invalid @enderror" name="peserta_id" id="peserta-id" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select pesertum') }} --</option>
                
                        @foreach ($pesertas as $pesertum)
                            <option value="{{ $pesertum?->id }}" {{ isset($pendaftaran) && $pendaftaran?->peserta_id == $pesertum?->id ? 'selected' : (old('peserta_id') == $pesertum?->id ? 'selected' : '') }}>
                                {{ $pesertum?->id }}
                            </option>
                        @endforeach
            </select>
            @error('peserta_id')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="status">{{ __('Status') }}</label>
            <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select status') }} --</option>
                <option value="Waiting" {{ isset($pendaftaran) && $pendaftaran->status == 'Waiting' ? 'selected' : (old('status') == 'Waiting' ? 'selected' : '') }}>Waiting</option>
		<option value="Approved" {{ isset($pendaftaran) && $pendaftaran->status == 'Approved' ? 'selected' : (old('status') == 'Approved' ? 'selected' : '') }}>Approved</option>
		<option value="Rejected" {{ isset($pendaftaran) && $pendaftaran->status == 'Rejected' ? 'selected' : (old('status') == 'Rejected' ? 'selected' : '') }}>Rejected</option>			
            </select>
            @error('status')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="tanggal-pengajuan">{{ __('Tanggal Pengajuan') }}</label>
            <input type="datetime-local" name="tanggal_pengajuan" id="tanggal-pengajuan" class="form-control @error('tanggal_pengajuan') is-invalid @enderror" value="{{ isset($pendaftaran) && $pendaftaran?->tanggal_pengajuan ? $pendaftaran?->tanggal_pengajuan?->format('Y-m-d\TH:i') : old('tanggal_pengajuan') }}" placeholder="{{ __('Tanggal Pengajuan') }}" required />
            @error('tanggal_pengajuan')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>