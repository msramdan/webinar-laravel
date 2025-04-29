<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama">{{ __('Nama') }}</label>
            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror"
                value="{{ isset($peserta) ? $peserta->nama : old('nama') }}" placeholder="{{ __('Nama') }}" required />
            @error('nama')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="kampus_id">{{ __('Kampus') }}</label>
            <select name="kampus_id" id="kampus_id" class="form-select select2 @error('kampus_id') is-invalid @enderror"
                required>
                <option value="">-- {{ __('Pilih Kampus') }} --</option>
                @foreach ($kampus as $item)
                    <option value="{{ $item->id }}"
                        {{ isset($peserta) && $peserta->kampus_id == $item->id ? 'selected' : (old('kampus_id') == $item->id ? 'selected' : '') }}>
                        {{ $item->nama_kampus }}
                    </option>
                @endforeach
            </select>
            @error('kampus_id')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            <label for="no-telepon">{{ __('No Telepon') }}</label>
            <input type="tel" name="no_telepon" id="no-telepon"
                class="form-control @error('no_telepon') is-invalid @enderror"
                value="{{ isset($peserta) ? $peserta->no_telepon : old('no_telepon') }}"
                placeholder="{{ __('No Telepon') }}" required />
            @error('no_telepon')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ isset($peserta) ? $peserta->email : old('email') }}" placeholder="{{ __('Email') }}"
                required />
            @error('email')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="alamat">{{ __('Alamat') }}</label>
            <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror"
                placeholder="{{ __('Alamat') }}" required>{{ isset($peserta) ? $peserta->alamat : old('alamat') }}</textarea>
            @error('alamat')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="is_verified">{{ __('Status Verifikasi') }}</label>
            <select name="is_verified" id="is_verified" class="form-select @error('is_verified') is-invalid @enderror"
                required>
                <option value="No"
                    {{ (isset($peserta) && $peserta->is_verified == 'No') || old('is_verified') == 'No' ? 'selected' : '' }}>
                    Belum Diverifikasi</option>
                <option value="Yes"
                    {{ (isset($peserta) && $peserta->is_verified == 'Yes') || old('is_verified') == 'Yes' ? 'selected' : '' }}>
                    Sudah Diverifikasi</option>
            </select>
            @error('is_verified')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}"
                {{ empty($peserta) ? ' required' : '' }} />
            @error('password')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
            @isset($peserta)
                <div id="PasswordHelpBlock" class="form-text">
                    {{ __('Biarkan kosong jika tidak ingin mengubah password.') }}
                </div>
            @endisset
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="password-confirmation">{{ __('Konfirmasi Password') }}</label> <input type="password"
                name="password_confirmation" id="password-confirmation" class="form-control"
                placeholder="{{ __('Konfirmasi Password') }}" {{ empty($peserta) ? ' required' : '' }} />
        </div>
    </div>
</div>
