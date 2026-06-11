<?php

use App\Services\MitecoService;
use App\Models\PreciosProducto;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "--- TESTING MITECO INTEGRATION ---\n";
    
    // 1. Check if we can query local prices
    echo "Fetching prices from database:\n";
    $stations = [
        1 => 'E.S. Vistalegre (Utrera)',
        2 => 'Ronda Norte (Sevilla)',
        3 => 'E.S. Rodalabota (El Cuervo)',
        4 => 'E.S. Atenas (Lebrija)',
    ];

    foreach ($stations as $code => $name) {
        $pvpGoa = PreciosProducto::where('CodigoEstacion', $code)
            ->where('CodigoProducto', '1')
            ->value('PVP');

        $pvpG95e5 = PreciosProducto::where('CodigoEstacion', $code)
            ->where('CodigoProducto', '2')
            ->value('PVP');

        echo "  Station {$code} ({$name}):\n";
        echo "    Gasoleo A: " . ($pvpGoa !== null ? number_format((float)$pvpGoa, 3, ',', '') : 'NULL') . "\n";
        echo "    Gasolina 95 E5: " . ($pvpG95e5 !== null ? number_format((float)$pvpG95e5, 3, ',', '') : 'NULL') . "\n";
    }

    // 2. Dry run of MitecoService serialization
    echo "\nGenerating dry-run payload:\n";
    $service = new MitecoService();
    
    // Use reflection to call protected stationsConfig or mock upload
    $reflector = new ReflectionClass($service);
    $stationsConfigProp = $reflector->getProperty('stationsConfig');
    $stationsConfigProp->setAccessible(true);
    $stationsConfig = $stationsConfigProp->getValue($service);

    $precios = [];
    $vigencia = now('Europe/Madrid')->addMinutes(75);
    $fechaiper = $vigencia->format('d/m/Y');
    $horaiper = $vigencia->format('H:i');

    foreach ($stationsConfig as $stationCode => $config) {
        $pvpGoa = PreciosProducto::where('CodigoEstacion', $stationCode)
            ->where('CodigoProducto', '1')
            ->value('PVP');

        $pvpG95e5 = PreciosProducto::where('CodigoEstacion', $stationCode)
            ->where('CodigoProducto', '2')
            ->value('PVP');

        $stationData = [
            'firma' => env('MITECO_FIRMA') ?: 'IND',
            'num_reg' => $config['num_reg'],
            'margen' => $config['margen'],
            'fechaiper' => $fechaiper,
            'horaiper' => $horaiper,
            'tventa_coop' => 'P',
        ];

        if ($pvpG95e5 !== null) {
            $stationData['pvpg95e5'] = number_format((float) $pvpG95e5, 3, ',', '');
        }
        if ($pvpGoa !== null) {
            $stationData['pvpgoa'] = number_format((float) $pvpGoa, 3, ',', '');
        }
        $precios[] = $stationData;
    }

    $payload = [
        'tipo' => 'ITGFS',
        'envio' => 'ITGFS' . str_pad(env('MITECO_REMITENTE') ?: 'IND', 3, 'X', STR_PAD_RIGHT) . now('Europe/Madrid')->format('Ymd'),
        'precios' => $precios,
    ];

    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 3. Try to authenticate (if credentials are set)
    echo "\nAttempting API login:\n";
    $user = config('services.miteco.user');
    $pass = config('services.miteco.password');
    echo "  MITECO_USER configured: " . ($user ? 'YES (' . $user . ')' : 'NO') . "\n";
    echo "  MITECO_PASSWORD configured: " . ($pass ? 'YES' : 'NO') . "\n";

    if ($user && $pass) {
        $token = $service->login();
        if ($token) {
            echo "  Login SUCCESS! Token: " . substr($token, 0, 20) . "...\n";
            
            // Perform upload check (dry run / actual upload)
            echo "\nPerforming upload validation...\n";
            $res = $service->uploadPrices();
            print_r($res);
        } else {
            echo "  Login FAILED. Check logs.\n";
        }
    } else {
        echo "  Credentials not configured in .env. Skipping login test.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
