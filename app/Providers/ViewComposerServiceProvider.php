<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer(['users.create', 'users.edit'], function ($view) {
            return $view->with(
                'roles',
                Role::select('id', 'name')->get()
            );
        });


		View::composer(['pendaftaran.create', 'pendaftaran.edit'], function ($view) {
            return $view->with(
                'sesis',
                \App\Models\Sesi::select('id')->get()
            );
        });

View::composer(['pendaftaran.create', 'pendaftaran.edit'], function ($view) {
            return $view->with(
                'peserta',
                \App\Models\Pesertum::select('id', 'nama')->get()
            );
        });

	}
}
