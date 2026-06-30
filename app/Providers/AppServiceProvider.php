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
        if (app()->runningInConsole()) {
            return;
        }

        $host = request()->header('X-Forwarded-Host') ?? request()->header('Host');
        if ($host) {
            $host = trim(explode(',', $host)[0]);
            $isLocal = str_contains($host, '.ddev.site') || str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');
            $proto = $isLocal ? (request()->header('X-Forwarded-Proto') ?? (request()->secure() ? 'https' : 'http')) : 'https';
            $baseUrl = $proto . '://' . $host;
            config(['app.url' => $baseUrl]);
            config(['filesystems.disks.public.url' => $baseUrl . '/storage']);
        }

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
        if (!app()->runningInConsole() || app()->environment() !== 'testing') {
            try {
                \Illuminate\Support\Facades\View::share('homeConfig', HomeConfig::first());
            } catch (\Throwable $e) {
                // Ignore during migrations or console testing
            }
        } else {
            // For testing, share null or mock
            \Illuminate\Support\Facades\View::share('homeConfig', null);
        }
        \Illuminate\Support\Facades\View::share('gasolineras', []);

    }
}
