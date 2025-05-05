@extends('panel-peserta.layouts.master')
@section('title', 'Seminar Saya')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Update Profil</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('panel-peserta.profil.update') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" name="nama" id="nama"
                                    value="{{ old('nama', $peserta->nama ?? '') }}"
                                    class="form-control @error('nama') is-invalid @enderror">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="no_telepon">No. Telepon</label>
                                <input type="text" name="no_telepon" id="no_telepon"
                                    value="{{ old('no_telepon', $peserta->no_telepon ?? '') }}"
                                    class="form-control @error('no_telepon') is-invalid @enderror">
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $peserta->email ?? '') }}"
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $peserta->alamat ?? '') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="kampus_id">Kampus</label>
                                <select name="kampus_id" id="kampus_id"
                                    class="form-control select2 @error('kampus_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kampus --</option>
                                    @foreach ($kampus as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('kampus_id', $peserta->kampus_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_kampus }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kampus_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>
                            <h6>Ganti Password (Opsional)</h6>

                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#kampus_id').select2({
                    placeholder: '-- Pilih Kampus --',
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endpush

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container .select2-selection--single {
                height: 38px;
                padding-top: 4px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }
        </style>
    @endpush
@endsection
