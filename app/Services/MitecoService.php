<?php

namespace App\Services;

use App\Models\PreciosProducto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MitecoService
{
    protected string $baseUrl = 'https://energia.serviciosmin.gob.es/rispapi';
    protected string $firma;

    // Hardcoded station mappings for Utrecar S.L. (NIF B41527250)
    protected array $stationsConfig = [
        1 => [
            'name' => 'E.S. VISTALEGRE (Utrera)',
            'num_reg' => 'AND41.1.00357',
            'margen' => 'N',
            'env_key' => 'UTRERA',
        ],
        2 => [
            'name' => 'RONDA NORTE (Sevilla)',
            'num_reg' => 'AND41/35973',
            'margen' => 'N',
            'env_key' => 'RONDA_NORTE',
        ],
        3 => [
            'name' => 'E.S. RODALABOTA (El Cuervo)',
            'num_reg' => 'AND201699903784267',
            'margen' => 'N',
            'env_key' => 'EL_CUERVO',
        ],
        4 => [
            'name' => 'E.S. ATENAS (Lebrija)',
            'num_reg' => 'AND201599901562684',
            'margen' => 'D',
            'env_key' => 'LEBRIJA',
        ],
    ];

    public function __construct()
    {
        $this->firma = config('services.miteco.firma', 'IND');
    }

    /**
     * Authenticate with the MITECO API and retrieve the Bearer Token.
     */
    public function login(string $user, string $password): ?string
    {
        if (empty($user) || empty($password)) {
            Log::error('MitecoService: Missing credentials for login.');
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/usuario/login', [
                    'usuario' => $user,
                    'password' => $password,
                ]);

            if ($response->failed()) {
                Log::error("MitecoService login failed for {$user}: " . $response->status() . ' - ' . $response->body());
                return null;
            }

            $token = $response->json('token');
            if (empty($token)) {
                Log::error("MitecoService: Login response for {$user} did not contain a token.");
                return null;
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error("MitecoService login exception for {$user}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch prices from virtusgesnet and upload them to MITECO for all stations.
     */
    public function uploadPrices(): array
    {
        // 1. Fetch current prices from database for comparison
        $currentPrices = [];
        foreach ($this->stationsConfig as $stationCode => $config) {
            $pvpGoa = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '1')
                ->value('PVP');
            $pvpG95e5 = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '2')
                ->value('PVP');
            
            $currentPrices[$stationCode] = [
                'name' => $config['name'],
                'goa' => $pvpGoa !== null ? floatval($pvpGoa) : null,
                'g95e5' => $pvpG95e5 !== null ? floatval($pvpG95e5) : null,
            ];
        }

        // 2. Determine if prices changed compared to last upload
        $lastPrices = Cache::get('miteco_last_uploaded_prices');
        $pricesChanged = false;

        if (!$lastPrices) {
            $pricesChanged = true;
        } else {
            foreach ($currentPrices as $stationCode => $prices) {
                if (!isset($lastPrices[$stationCode])) {
                    $pricesChanged = true;
                    break;
                }
                if ($lastPrices[$stationCode]['goa'] !== $prices['goa'] || 
                    $lastPrices[$stationCode]['g95e5'] !== $prices['g95e5']) {
                    $pricesChanged = true;
                    break;
                }
            }
        }

        // 3. Apply Monday 06:00 AM constraint if prices haven't changed
        if (!$pricesChanged) {
            $now = now('Europe/Madrid');
            // Since this runs every 5 minutes, we check if it's Monday between 06:00 and 06:04
            $isMonday6AM = $now->isMonday() && $now->hour === 6 && $now->minute < 5;

            if (!$isMonday6AM) {
                $msg = 'Los precios no han cambiado y no es Lunes a las 06:00 AM (Hora Española). Envío omitido.';
                Log::info("MitecoService: " . $msg);

                $statusData = [
                    'timestamp' => now('Europe/Madrid')->toIso8601String(),
                    'status' => 'skipped',
                    'message' => $msg,
                    'prices' => $currentPrices,
                ];
                Cache::put('miteco_last_update_status', $statusData);

                return [
                    'success' => true,
                    'skipped' => true,
                    'message' => $msg,
                    'prices' => $currentPrices,
                ];
            }
        }

        // 4. Perform the upload (grouped or individual)
        $groupUser = config('services.miteco.user');
        $groupPassword = config('services.miteco.password');

        if ($groupUser && $groupPassword) {
            $res = $this->uploadPricesGrouped($groupUser, $groupPassword);
        } else {
            $res = $this->uploadPricesIndividual();
        }

        // 5. Save execution outcome to Cache
        if ($res['success']) {
            Cache::put('miteco_last_uploaded_prices', $currentPrices);

            $statusData = [
                'timestamp' => now('Europe/Madrid')->toIso8601String(),
                'status' => 'success',
                'message' => 'Precios actualizados con éxito en Miteco.',
                'prices' => $currentPrices,
                'data' => $res['data'] ?? ($res['results'] ?? null),
            ];
            Cache::put('miteco_last_update_status', $statusData);
        } else {
            $statusData = [
                'timestamp' => now('Europe/Madrid')->toIso8601String(),
                'status' => 'failed',
                'message' => $res['message'] ?? 'Fallo en la comunicación con Miteco.',
                'prices' => $currentPrices,
                'errors' => $res['results'] ?? null,
            ];
            Cache::put('miteco_last_update_status', $statusData);
        }

        return $res;
    }

    /**
     * Individual upload helper (formerly part of uploadPrices).
     */
    protected function uploadPricesIndividual(): array
    {

        $results = [];
        $overallSuccess = true;

        // MITECO requires vigencia to be at least 1 hour in the future, and maximum 3 days.
        // We set it to current time + 75 minutes (1h 15m) to safely clear the 1-hour constraint.
        $vigencia = now('Europe/Madrid')->addMinutes(75);
        $fechaiper = $vigencia->format('d/m/Y');
        $horaiper = $vigencia->format('H:i');
        $dateStr = now('Europe/Madrid')->format('Ymd');

        foreach ($this->stationsConfig as $stationCode => $config) {
            $envKey = $config['env_key'];
            $user = config("services.miteco.stations.{$envKey}.user");
            $password = config("services.miteco.stations.{$envKey}.password");

            if (!$user || !$password) {
                Log::warning("MitecoService: Missing credentials for station {$config['name']} (Key: {$envKey}).");
                $results[$config['name']] = [
                    'success' => false,
                    'message' => 'Missing credentials',
                ];
                $overallSuccess = false;
                continue;
            }

            $token = $this->login($user, $password);
            if (!$token) {
                $results[$config['name']] = [
                    'success' => false,
                    'message' => 'Authentication failed',
                ];
                $overallSuccess = false;
                continue;
            }

            // Retrieve current prices from preciosdeproductos table
            // Code 1 = Gasoleo A
            // Code 2 = Gasolina 95 (Sin Plomo 95)
            $pvpGoa = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '1')
                ->value('PVP');

            $pvpG95e5 = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '2')
                ->value('PVP');

            if ($pvpGoa === null && $pvpG95e5 === null) {
                Log::warning("MitecoService: No prices found for station {$config['name']} (Code {$stationCode}) in database.");
                $results[$config['name']] = [
                    'success' => false,
                    'message' => 'No price data found in database',
                ];
                $overallSuccess = false;
                continue;
            }

            $stationData = [
                'firma' => $config['firma'] ?? 'IND',
                'num_reg' => $config['num_reg'],
                'margen' => $config['margen'],
                'fechaiper' => $fechaiper,
                'horaiper' => $horaiper,
                'tventa_coop' => 'P', // Public sale
            ];

            // MITECO expects prices formatted with 3 decimals and comma decimal separator (e.g. "1,499")
            if ($pvpG95e5 !== null) {
                $stationData['pvpg95e5'] = number_format((float) $pvpG95e5, 3, ',', '');
            }

            if ($pvpGoa !== null) {
                $stationData['pvpgoa'] = number_format((float) $pvpGoa, 3, ',', '');
            }

            // ZZZ code in ITGFSZZZAAAAMMDD. Derived from the specific user, or default to 'IND'
            $remitente = substr($user, 0, 3);
            $envioCode = 'ITGFS' . str_pad($remitente, 3, 'X', STR_PAD_RIGHT) . $dateStr;

            $payload = [
                'tipo' => 'ITGFS',
                'envio' => $envioCode,
                'precios' => [$stationData],
            ];

            try {
                Log::info("MitecoService: Sending prices to MITECO for station {$config['name']}...", ['envio' => $envioCode, 'payload' => $payload]);

                $response = Http::timeout(30)
                    ->withToken($token)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])
                    ->post($this->baseUrl . '/v1/envio/itgfs', $payload);

                if ($response->failed()) {
                    Log::error("MitecoService upload failed for station {$config['name']}: " . $response->status() . ' - ' . $response->body());
                    $results[$config['name']] = [
                        'success' => false,
                        'status' => $response->status(),
                        'message' => $response->body(),
                    ];
                    $overallSuccess = false;
                } else {
                    $responseData = $response->json();
                    Log::info("MitecoService: Prices uploaded successfully for station {$config['name']}.", $responseData);
                    $results[$config['name']] = [
                        'success' => true,
                        'data' => $responseData,
                    ];
                }

            } catch (\Throwable $e) {
                Log::error("MitecoService upload exception for station {$config['name']}: " . $e->getMessage());
                $results[$config['name']] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
                $overallSuccess = false;
            }
        }

        return [
            'success' => $overallSuccess,
            'results' => $results,
        ];
    }

    /**
     * Grouped upload of all stations under a single user/password.
     */
    protected function uploadPricesGrouped(string $user, string $password): array
    {
        $token = $this->login($user, $password);
        if (!$token) {
            Log::error('MitecoService: Grouped login failed.');
            return [
                'success' => false,
                'message' => 'Grouped login failed',
            ];
        }

        // MITECO requires vigencia to be at least 1 hour in the future, and maximum 3 days.
        // We set it to current time + 75 minutes (1h 15m) to safely clear the 1-hour constraint.
        $vigencia = now('Europe/Madrid')->addMinutes(75);
        $fechaiper = $vigencia->format('d/m/Y');
        $horaiper = $vigencia->format('H:i');
        $dateStr = now('Europe/Madrid')->format('Ymd');

        $precios = [];
        $results = [];

        foreach ($this->stationsConfig as $stationCode => $config) {
            // Retrieve current prices from preciosdeproductos table
            // Code 1 = Gasoleo A
            // Code 2 = Gasolina 95 (Sin Plomo 95)
            $pvpGoa = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '1')
                ->value('PVP');

            $pvpG95e5 = PreciosProducto::where('CodigoEstacion', $stationCode)
                ->where('CodigoProducto', '2')
                ->value('PVP');

            if ($pvpGoa === null && $pvpG95e5 === null) {
                Log::warning("MitecoService: No prices found for station {$config['name']} (Code {$stationCode}) in database.");
                $results[$config['name']] = [
                    'success' => false,
                    'message' => 'No price data found in database',
                ];
                continue;
            }

            $stationData = [
                'firma' => $config['firma'] ?? 'IND',
                'num_reg' => $config['num_reg'],
                'margen' => $config['margen'],
                'fechaiper' => $fechaiper,
                'horaiper' => $horaiper,
                'tventa_coop' => 'P', // Public sale
            ];

            // MITECO expects prices formatted with 3 decimals and comma decimal separator (e.g. "1,499")
            if ($pvpG95e5 !== null) {
                $stationData['pvpg95e5'] = number_format((float) $pvpG95e5, 3, ',', '');
            }

            if ($pvpGoa !== null) {
                $stationData['pvpgoa'] = number_format((float) $pvpGoa, 3, ',', '');
            }

            $precios[] = $stationData;
            $results[$config['name']] = [
                'success' => true,
                'message' => 'Added to batch',
            ];
        }

        if (empty($precios)) {
            return [
                'success' => false,
                'message' => 'No station data to upload.',
                'results' => $results,
            ];
        }

        // ZZZ code in ITGFSZZZAAAAMMDD. Derived from the firma or default to 'IND'
        $remitente = str_pad(substr($this->firma, 0, 3), 3, 'X', STR_PAD_RIGHT);
        $envioCode = 'ITGFS' . $remitente . $dateStr;

        $payload = [
            'tipo' => 'ITGFS',
            'envio' => $envioCode,
            'precios' => $precios,
        ];

        try {
            Log::info("MitecoService: Sending grouped prices to MITECO...", ['envio' => $envioCode, 'payload' => $payload]);

            $response = Http::timeout(30)
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/envio/itgfs', $payload);

            if ($response->failed()) {
                Log::error("MitecoService grouped upload failed: " . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => $response->body(),
                    'results' => $results,
                ];
            }

            $responseData = $response->json();
            Log::info("MitecoService: Grouped prices uploaded successfully.", $responseData);
            return [
                'success' => true,
                'data' => $responseData,
                'results' => $results,
            ];

        } catch (\Throwable $e) {
            Log::error("MitecoService grouped upload exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'results' => $results,
            ];
        }
    }
}
