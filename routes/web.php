<?php

use App\Http\Controllers\PembicaraController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\SponsorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/', fn() => view('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'));

    Route::get('/profile', App\Http\Controllers\ProfileController::class)->name('profile');

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleAndPermissionController::class);

    Route::resource('backup-database', App\Http\Controllers\BackupDatabaseController::class)->only(['index']);
    Route::get('/backup/download', [App\Http\Controllers\BackupDatabaseController::class, 'downloadBackup'])->name('backup.download');
    Route::resource('peserta', App\Http\Controllers\PesertaController::class);
    Route::resource('seminar', App\Http\Controllers\SeminarController::class);

    Route::prefix('seminar')->group(function () {
        Route::controller(SeminarController::class)->group(function () {
            Route::get('/{id}/pembicara', 'showPembicara')->name('seminar.pembicara.show');
        });

        Route::controller(SeminarController::class)->group(function () {
            Route::get('/{id}/sponsor', 'showSponsor')->name('seminar.sponsor.show');
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
    });
});
