<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$route = Route::getRoutes()->getByName('filament.admin.pages.dashboard');
if ($route) {
    echo "Middleware on dashboard route: " . implode(', ', $route->gatherMiddleware()) . "\n";
} else {
    echo "Dashboard route not found\n";
}
