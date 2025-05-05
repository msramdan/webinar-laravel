<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanPresensiController;
use App\Http\Controllers\PanelPeserta\AuthController;
use App\Http\Controllers\PanelPeserta\DashboardPesertaController;
use App\Http\Controllers\PembicaraController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\SesiSeminarController;
use App\Http\Controllers\SponsorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::get('/', fn() => Redirect::route('panel-peserta.login'));
Route::redirect('/panel-peserta', '/panel-peserta/login');
Route::prefix('panel-peserta')->group(function () {
    Route::middleware('guest:panel-peserta')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('panel-peserta.register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('panel-peserta.login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::get('/verify-email/{email}/{token}', [AuthController::class, 'verifyEmail'])
        ->name('panel-peserta.verify.email');

    Route::post('/logout', [AuthController::class, 'logout'])->name('panel-peserta.logout');

    Route::middleware('auth:panel-peserta')->group(function () {
        Route::get('/seminar-saya', [DashboardPesertaController::class, 'seminarSaya'])
            ->name('panel-peserta.seminarSaya');
        Route::get('/semua-seminar', [DashboardPesertaController::class, 'semuaSeminar'])
            ->name('panel-peserta.semuaSeminar');
        Route::get('/{id}/peserta-download-qrcode', [DashboardPesertaController::class, 'downloadQRCode'])
            ->name('pendaftaran.qrcode.download.peserta');
        Route::get('/seminar/{id}/sesi', [DashboardPesertaController::class, 'lihatSesi'])
            ->name('panel-peserta.lihatSesi');
        Route::post('/seminar/register', [DashboardPesertaController::class, 'register'])
            ->name('panel-peserta.register.seminar');
        Route::get('/sertifikat/download/{pendaftaranId}', [DashboardPesertaController::class, 'downloadSertifikat'])
            ->name('panel-peserta.sertifikat.download');
        Route::get('/generate-qrcode-data/{id}', [DashboardPesertaController::class, 'generateQrCodeData'])
            ->name('panel-peserta.qrcode.generate');
        Route::get('/profil', [DashboardPesertaController::class, 'getProfile'])
            ->name('panel-peserta.profil');
        Route::post('/profil/update', [DashboardPesertaController::class, 'updateProfile'])
            ->name('panel-peserta.profil.update');
    });
});

// Middleware 'auth' dan 'web' untuk admin panel
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', App\Http\Controllers\ProfileController::class)->name('profile');

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleAndPermissionController::class);

    Route::resource('kampus', App\Http\Controllers\KampusController::class)->middleware('auth');

    Route::resource('backup-database', App\Http\Controllers\BackupDatabaseController::class)->only(['index']);
    Route::get('/backup/download', [App\Http\Controllers\BackupDatabaseController::class, 'downloadBackup'])->name('backup.download');
    Route::resource('peserta', App\Http\Controllers\PesertaController::class);
    Route::resource('seminar', App\Http\Controllers\SeminarController::class);
    Route::resource('pendaftaran', App\Http\Controllers\PendaftaranController::class)->only(['index']);
    Route::resource('scan', App\Http\Controllers\ScanController::class)->only(['index', 'show']);
    Route::post('/scan/proses', [ScanController::class, 'prosesScan'])->name('scan.process');

    Route::prefix('pendaftaran')->controller(PendaftaranController::class)->group(function () {
        Route::get('/peserta-sesi/{id}', 'pesertaSesi')->name('pendaftaran.peserta.sesi');
        Route::post('/', 'store')->name('pendaftaran.store');
        Route::put('/{id}', 'update')->name('pendaftaran.update');
        Route::delete('/{id}', 'destroy')->name('pendaftaran.destroy');
        Route::get('/{id}/generate-qrcode', 'generateQRCode')->name('pendaftaran.qrcode.generate');
        Route::get('/{id}/download-qrcode', 'downloadQRCode')->name('pendaftaran.qrcode.download');
    });

    Route::prefix('laporan')->controller(LaporanPresensiController::class)->group(function () {
        Route::get('/', 'index')->name('laporan.index');
        Route::get('/sesi-seminar/{id}', 'laporanSesiSeminar')->name('laporan.sesi.seminar');
        Route::get('/laporan-sesi-seminar/{seminar}', 'getData')->name('laporan.sesi.data');
        Route::get('/download-presensi/{sesi}', 'downloadPresensi')->name('laporan.presensi.download');
    });

    Route::prefix('seminar')->group(function () {
        Route::controller(SeminarController::class)->group(function () {
            Route::get('/{id}/pembicara', 'showPembicara')->name('seminar.pembicara.show');
        });

        Route::controller(SeminarController::class)->group(function () {
            Route::get('/{id}/sponsor', 'showSponsor')->name('seminar.sponsor.show');
        });

        Route::controller(SeminarController::class)->group(function () {
            Route::get('/{id}/sesi-seminat', 'showSesi')->name('seminar.sesi.show');
        });

        Route::prefix('{seminar}/pembicara')->controller(PembicaraController::class)->group(function () {
            Route::get('/', 'index')->name('pembicara.index');
            Route::get('/data', 'getData')->name('pembicara.data');
            Route::post('/', 'store')->name('pembicara.store');
            Route::get('/{id}', 'show')->name('pembicara.show');
            Route::put('/{id}', 'update')->name('pembicara.update');
            Route::delete('/{id}', 'destroy')->name('pembicara.destroy');
        });

        Route::prefix('{seminar}/sponsor')->controller(SponsorController::class)->group(function () {
            Route::get('/', 'index')->name('sponsor.index');
            Route::get('/data', 'getData')->name('sponsor.data');
            Route::post('/', 'store')->name('sponsor.store');
            Route::get('/{id}', 'show')->name('sponsor.show');
            Route::put('/{id}', 'update')->name('sponsor.update');
            Route::delete('/{id}', 'destroy')->name('sponsor.destroy');
        });

        Route::prefix('{seminar}/sesi')->controller(SesiSeminarController::class)->group(function () {
            Route::get('/', 'index')->name('sesi.index');
            Route::get('/data', 'getData')->name('sesi.data');
            Route::post('/', 'store')->name('sesi.store');
            Route::get('/{id}', 'show')->name('sesi.show');
            Route::put('/{id}', 'update')->name('sesi.update');
            Route::delete('/{id}', 'destroy')->name('sesi.destroy');
        });
    });
});
