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
            <label for="kampus_asal">{{ __('Kampus Asal') }}</label>
            <select name="kampus_asal" id="kampus_asal" class="form-control @error('kampus_asal') is-invalid @enderror" required>
                <option value="UGM" {{ isset($peserta) && $peserta->kampus_asal == 'UGM' ? 'selected' : '' }}>UGM</option>
                <option value="UNS" {{ isset($peserta) && $peserta->kampus_asal == 'UNS' ? 'selected' : '' }}>UNS</option>
                <option value="UNY" {{ isset($peserta) && $peserta->kampus_asal == 'UNY' ? 'selected' : '' }}>UNY</option>
                <option value="Lainnya" {{ isset($peserta) && $peserta->kampus_asal == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('kampus_asal')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6" id="kampus_lainnya_div" style="{{ old('kampus_asal') == 'Lainnya' || (isset($peserta) && $peserta->kampus_asal == 'Lainnya') ? '' : 'display: none;' }}">
        <div class="form-group">
            <label for="kampus_lainnya">{{ __('Nama Kampus Lainnya') }}</label>
            <input type="text" name="kampus_lainnya" id="kampus_lainnya" class="form-control @error('kampus_lainnya') is-invalid @enderror"
                   value="{{ old('kampus_lainnya', isset($peserta) && $peserta->kampus_asal == 'Lainnya' ? $peserta->kampus_asal : '') }}"
                   placeholder="{{ __('Nama Kampus') }}" {{ old('kampus_asal') == 'Lainnya' || (isset($peserta) && $peserta->kampus_asal == 'Lainnya') ? 'required' : '' }} />
            @error('kampus_lainnya')
                <span class="text-danger">{{ $message }}</span>
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
                    {{ __('Leave the Password & Password Confirmation blank if you don`t want to change them.') }}
                </div>
            @endisset
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="password-confirmation">{{ __('Password Confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password-confirmation" class="form-control"
                placeholder="{{ __('Password Confirmation') }}" {{ empty($peserta) ? ' required' : '' }} />
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kampusAsal = document.getElementById('kampus_asal');
        const kampusLainnyaDiv = document.getElementById('kampus_lainnya_div');
        const kampusLainnya = document.getElementById('kampus_lainnya');

        // Toggle the visibility of the "kampus_lainnya" field
        function toggleKampusLainnya() {
            if (kampusAsal.value === 'Lainnya') {
                kampusLainnyaDiv.style.display = '';
                kampusLainnya.setAttribute('required', true);  // Make it required
            } else {
                kampusLainnyaDiv.style.display = 'none';
                kampusLainnya.removeAttribute('required');  // Remove the required attribute
            }
        }

        // Initial state check on page load
        toggleKampusLainnya();

        // Add event listener to the dropdown to toggle the field visibility when changed
        kampusAsal.addEventListener('change', toggleKampusLainnya);
    });
</script>
@endpush
