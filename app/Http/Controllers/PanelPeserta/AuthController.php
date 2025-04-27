<?php

namespace App\Http\Controllers\PanelPeserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('panel-peserta.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_telepon' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:peserta,email'],
            'alamat' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',

            'no_telepon.required' => 'No. Telepon wajib diisi.',
            'no_telepon.string' => 'No. Telepon harus berupa teks.',
            'no_telepon.max' => 'No. Telepon maksimal 15 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $id = DB::table('peserta')->insertGetId([
            'nama' => $request->nama,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peserta = DB::table('peserta')->where('id', $id)->first();

        Auth::guard('panel-peserta')->loginUsingId($peserta->id);

        return redirect()->route('panel-peserta.seminarSaya');
    }

    public function showLoginForm()
    {
        $sponsors = DB::table('sponsor')
            ->join('seminar', 'sponsor.seminar_id', '=', 'seminar.id')
            ->where('seminar.is_active', 'Yes')
            ->select('sponsor.*')
            ->get();

        return view('panel-peserta.auth.login', compact('sponsors'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::guard('panel-peserta')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->route('panel-peserta.seminarSaya');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('panel-peserta')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('panel-peserta.login');
    }
}
