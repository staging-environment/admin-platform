<?php

use App\Services\MitecoService;
use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$idEnvio = $argv[1] ?? '1351076';

try {
    echo "Querying status for Envio ID: {$idEnvio}\n";
    $service = new MitecoService();
    
    // Use reflection to login and call the API
    $reflector = new ReflectionClass($service);
    $loginMethod = $reflector->getMethod('login');
    $loginMethod->setAccessible(true);
    
    $user = config('services.miteco.user');
    $password = config('services.miteco.password');
    
    echo "Logging in with {$user}...\n";
    $token = $loginMethod->invoke($service, $user, $password);
    
    if (!$token) {
        throw new Exception("Login failed");
    }
    
    $baseUrl = 'https://energia.serviciosmin.gob.es/rispapi';
    $response = Http::timeout(30)
        ->withToken($token)
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->get("{$baseUrl}/v1/Envio/getenvio/{$idEnvio}");
        
    echo "Status code: " . $response->status() . "\n";
    echo "Response:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
