<?php

namespace App\Services;

use App\Models\PreciosProducto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MitecoService
{
    protected string $baseUrl = 'https://energia.serviciosmin.gob.es/rispapi';
    protected ?string $user;
    protected ?string $password;
    protected string $firma;
    protected string $remitente;

    // Hardcoded station mappings for Utrecar S.L. (NIF B41527250)
    protected array $stationsConfig = [
        1 => [
            'name' => 'E.S. VISTALEGRE (Utrera)',
            'num_reg' => '6435',
            'margen' => 'N',
        ],
        2 => [
            'name' => 'RONDA NORTE (Sevilla)',
            'num_reg' => '7070',
            'margen' => 'N',
        ],
        3 => [
            'name' => 'E.S. RODALABOTA (El Cuervo)',
            'num_reg' => '13714',
            'margen' => 'N',
        ],
        4 => [
            'name' => 'E.S. ATENAS (Lebrija)',
            'num_reg' => '13194',
            'margen' => 'D',
        ],
    ];

    public function __construct()
    {
        $this->user = config('services.miteco.user');
        $this->password = config('services.miteco.password');
        $this->firma = config('services.miteco.firma', 'IND');
        
        // ZZZ code in ITGFSZZZAAAAMMDD. If not configured, default to the user username or 'IND'
        $this->remitente = env('MITECO_REMITENTE') ?: substr($this->user ?? 'IND', 0, 3);
    }

    /**
     * Authenticate with the MITECO API and retrieve the Bearer Token.
     */
    public function login(): ?string
    {
        if (empty($this->user) || empty($this->password)) {
            Log::error('MitecoService: Missing credentials in configuration.');
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/usuario/login', [
                    'usuario' => $this->user,
                    'password' => $this->password,
                ]);

            if ($response->failed()) {
                Log::error('MitecoService login failed: ' . $response->status() . ' - ' . $response->body());
                return null;
            }

            $token = $response->json('token');
            if (empty($token)) {
                Log::error('MitecoService: Login response did not contain a token.');
                return null;
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('MitecoService login exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch prices from virtusgesnet and upload them to MITECO.
     */
    public function uploadPrices(): array
    {
        $token = $this->login();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Authentication failed',
            ];
        }

        // MITECO requires vigencia to be at least 1 hour in the future, and maximum 3 days.
        // We set it to current time + 75 minutes (1h 15m) to safely clear the 1-hour constraint.
        $vigencia = now('Europe/Madrid')->addMinutes(75);
        $fechaiper = $vigencia->format('d/m/Y');
        $horaiper = $vigencia->format('H:i');

        $precios = [];

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
                continue;
            }

            $stationData = [
                'firma' => $this->firma,
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
        }

        if (empty($precios)) {
            return [
                'success' => false,
                'message' => 'No price data found to upload',
            ];
        }

        // Format: ITGFS + ZZZ (remitente) + AAAAMMDD (date)
        $dateStr = now('Europe/Madrid')->format('Ymd');
        $envioCode = 'ITGFS' . str_pad($this->remitente, 3, 'X', STR_PAD_RIGHT) . $dateStr;

        $payload = [
            'tipo' => 'ITGFS',
            'envio' => $envioCode,
            'precios' => $precios,
        ];

        try {
            Log::info('MitecoService: Sending prices to MITECO...', ['envio' => $envioCode, 'payload' => $payload]);

            $response = Http::timeout(30)
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/envio/itgfs', $payload);

            if ($response->failed()) {
                Log::error('MitecoService upload failed: ' . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => $response->body(),
                ];
            }

            $responseData = $response->json();
            Log::info('MitecoService: Prices uploaded successfully.', $responseData);

            return [
                'success' => true,
                'data' => $responseData,
            ];

        } catch (\Throwable $e) {
            Log::error('MitecoService upload exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
