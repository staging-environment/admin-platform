<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Middleware\RedirectToDefaultPanelPage;

$user = \App\Models\User::where('email', 'perdonero@gmail.com')->first();
auth()->login($user);

$request = Request::create('http://utrecar.com/admin', 'GET');
$request->setLaravelSession($app['session']->driver());

$middleware = new RedirectToDefaultPanelPage();
$response = $middleware->handle($request, function($req) {
    return response("Route handled");
});

echo "Response status: " . $response->getStatusCode() . "\n";
echo "Response headers: " . json_encode($response->headers->all()) . "\n";
echo "Done!\n";
