<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\HomeConfig;

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
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            $_SERVER['HTTPS'] = 'on';
            request()->server->set('HTTPS', 'on');
            request()->headers->set('X-Forwarded-Proto', 'https');
        }

        if (str_contains(request()->path(), 'upload-file')) {
            \Illuminate\Support\Facades\Log::info('Signature Debug', [
                'request_url' => request()->url(),
                'request_fullUrl' => request()->fullUrl(),
                'config_app_url' => config('app.url'),
                'has_valid_signature' => request()->hasValidSignature(),
                'x_forwarded_proto' => request()->header('X-Forwarded-Proto'),
                'x_forwarded_port' => request()->header('X-Forwarded-Port'),
                'x_forwarded_host' => request()->header('X-Forwarded-Host'),
            ]);
        }


        // Share global data for all views
        \Illuminate\Support\Facades\View::share('homeConfig', HomeConfig::first());
        \Illuminate\Support\Facades\View::share('gasolineras', []);

    }
}
