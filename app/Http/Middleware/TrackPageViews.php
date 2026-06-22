<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageView;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests on HTML content
        if ($request->method() === 'GET' && 
            $response->getStatusCode() === 200 && 
            !$request->ajax() && 
            !$request->wantsJson()) {
            
            $path = $request->path();

            // Exclude system paths
            $excludes = [
                'livewire/*',
                'api/*',
                'debug-*',
                'storage/*',
                'vendor/*',
                'build/*',
                '_debugbar/*',
                'up' // Health check
            ];

            foreach ($excludes as $exclude) {
                if (Str::is($exclude, $path)) {
                    return $response;
                }
            }

            // Anonymize IP address by hashing it (GDPR compliant daily unique counting)
            $ip = $request->ip();
            $hashedIp = $ip ? hash('sha256', $ip . date('Y-m-d')) : null;

            try {
                PageView::create([
                    'url' => $request->fullUrl(),
                    'path' => '/' . ltrim($path, '/'),
                    'ip_address' => $hashedIp,
                    'user_agent' => substr($request->userAgent(), 0, 500),
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silently fail to not interrupt user experience if DB fails
                report($e);
            }
        }

        return $response;
    }
}
