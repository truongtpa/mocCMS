<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // vite.config.js build vào public/asset/admin/build (buildDirectory).
        // Mặc định @vite tìm ở public/build nên phải báo lại, không thì
        // "Vite manifest not found at: public/build/manifest.json".
        Vite::useBuildDirectory('asset/admin/build');

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
        });
    }
}
