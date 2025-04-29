<?php

namespace App\Http\Controllers\PanelPeserta;

use App\Http\Controllers\Controller;
use App\Models\{Kampus, Peserta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $kampus = Kampus::all();
        return view('panel-peserta.auth.register', compact('kampus'));
    }

    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'no_telepon'   => ['required', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:peserta,email'],
            'alamat'       => ['required', 'string'],
            'kampus_id'    => ['required', 'integer', Rule::exists('kampus', 'id')],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', 'captcha'],
        ], [
            'nama.required'         => 'Nama wajib diisi.',
            'nama.string'           => 'Nama harus berupa teks.',
            'nama.max'              => 'Nama maksimal 255 karakter.',

            'no_telepon.required'   => 'No. Telepon wajib diisi.',
            'no_telepon.string'     => 'No. Telepon harus berupa teks.',
            'no_telepon.max'        => 'No. Telepon maksimal 15 karakter.',
            'no_telepon.regex'      => 'No. Telepon hanya boleh berisi angka.',

            'email.required'        => 'Email wajib diisi.',
            'email.string'          => 'Email harus berupa teks.',
            'email.email'           => 'Format email tidak valid.',
            'email.max'             => 'Email maksimal 255 karakter.',
            'email.unique'          => 'Email sudah terdaftar.',

            'alamat.required'       => 'Alamat wajib diisi.',
            'alamat.string'         => 'Alamat harus berupa teks.',

            'kampus_id.required'    => 'Kampus wajib dipilih.',
            'kampus_id.integer'     => 'Kampus tidak valid.',
            'kampus_id.exists'      => 'Kampus yang dipilih tidak ditemukan.',

            'password.required'     => 'Password wajib diisi.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',

            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot.',
            'g-recaptcha-response.captcha' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
        ]);

        // 2. Buat token verifikasi
        $verification_token = Str::random(60);

        // 3. Simpan ke DB
        $peserta = Peserta::create([
            'nama'         => $request->nama,
            'no_telepon'   => $request->no_telepon,
            'email'        => $request->email,
            'alamat'       => $request->alamat,
            'kampus_id'    => $request->kampus_id,
            'password'     => Hash::make($request->password),
            'is_verified'  => 'No',
            'verification_token' => $verification_token,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 4. Kirim email verifikasi
        try {
            $peserta->notify(new CustomVerifyEmail($verification_token, $peserta));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi nanti.');
        }

        // 5. Redirect ke halaman login dengan pesan sukses
        return redirect()->route('panel-peserta.login')
            ->with('status', 'Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi akun.');
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


    public function verifyEmail(Request $request, $email, $token)
    {
        $peserta = Peserta::where('email', $email)->where('verification_token', $token)->first();

        if (!$peserta) {
            // Token tidak valid atau email tidak ditemukan
            return redirect()->route('panel-peserta.login')->withErrors(['verification' => 'Link verifikasi tidak valid atau kedaluwarsa.']);
        }

        if ($peserta->is_verified == 'Yes') {
            return redirect()->route('panel-peserta.login')->with('status', 'Email Anda sudah diverifikasi sebelumnya.');
        }

        // Update status verifikasi
        $peserta->is_verified = 'Yes';
        $peserta->verification_token = null; // Hapus token setelah verifikasi
        $peserta->save();

        return redirect()->route('panel-peserta.login')->with('status', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required', 'captcha'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot.',
            'g-recaptcha-response.captcha' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
        ]);

        // 2. Coba autentikasi
        if (Auth::guard('panel-peserta')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->remember)) {

            // 3. Dapatkan user yang terautentikasi
            $peserta = Auth::guard('panel-peserta')->user();

            // 4. Cek status verifikasi
            if ($peserta->is_verified !== 'Yes') {
                // Jika belum diverifikasi, logout dan beri pesan error
                Auth::guard('panel-peserta')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'verification' => 'Akun Anda belum diverifikasi. Silakan cek email Anda atau hubungi admin.', // Pesan error verifikasi
                ])->onlyInput('email');
            }

            // 5. Jika sudah diverifikasi, regenerasi session dan redirect
            $request->session()->regenerate();
            return redirect()->intended(route('panel-peserta.seminarSaya')); // Redirect ke tujuan awal atau dashboard
        }

        // 6. Jika autentikasi gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.', // Pesan error login standar
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
