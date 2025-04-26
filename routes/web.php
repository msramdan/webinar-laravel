<?php

use App\Http\Controllers\SeminarController;
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


    Route::prefix('seminar')->controller(SeminarController::class)->group(function () {
        Route::get('/pembicara/show/{id}', 'showpembicara')->name('seminar.pembicara.show');
    });
});
