@extends('panel-peserta.layouts.auth')
@section('title', 'Login')
@section('content')
    <div class="text-center">
        <h1 class="h4 text-gray-900 mb-4">Form Registrasi Peserta!</h1>
    </div>

    <form method="POST" action="{{ route('panel-peserta.register') }}">
        @csrf

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                value="{{ old('nama') }}" required autocomplete="name" autofocus>
            @error('nama')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="no_telepon" class="form-label">No. Telepon</label>
            <input id="no_telepon" type="text" class="form-control @error('no_telepon') is-invalid @enderror"
                name="no_telepon" value="{{ old('no_telepon') }}" required>
            @error('no_telepon')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea id="alamat" class="form-control @error('alamat') is-invalid @enderror" name="alamat" required>{{ old('alamat') }}</textarea>
            @error('alamat')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group mb-3">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" id="generate-password" title="Generate Password">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn btn-outline-secondary" type="button" id="toggle-passwords" title="Show/Hide Password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label">Confirm Password</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                autocomplete="new-password">
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>

    <div class="register-link text-center">
        Sudah punya akun? <a href="{{ route('panel-peserta.login') }}">Login disini</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate password functionality
            const generateBtn = document.getElementById('generate-password');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password-confirm');

            generateBtn.addEventListener('click', function() {
                const chars = '0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                const passwordLength = 8;
                let password = '';

                for (let i = 0; i <= passwordLength; i++) {
                    const randomNumber = Math.floor(Math.random() * chars.length);
                    password += chars.substring(randomNumber, randomNumber + 1);
                }

                passwordInput.value = password;
                confirmPasswordInput.value = password;
            });

            // Toggle password visibility for both fields
            const toggleBtn = document.getElementById('toggle-passwords');
            const eyeIcon = toggleBtn.querySelector('i');

            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                confirmPasswordInput.setAttribute('type', type);

                eyeIcon.classList.toggle('fa-eye-slash');
                eyeIcon.classList.toggle('fa-eye');
            });
        });
    </script>
@endsection
