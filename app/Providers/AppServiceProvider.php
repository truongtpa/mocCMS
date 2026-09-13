<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Vite::useBuildDirectory('asset/admin/build');

        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
