<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FuelMarketsService
{
    const GASOIL_CACHE_KEY = 'fuel_markets_gasoil';
    const RBOB_CACHE_KEY   = 'fuel_markets_rbob';
    const CACHE_TTL        = 120; // 2 minutos

    public function getGasoilLondres(): array
    {
        return Cache::get(self::GASOIL_CACHE_KEY, $this->emptyData('BZ=F'));
    }

    public function getRBOB(): array
    {
        return Cache::get(self::RBOB_CACHE_KEY, $this->emptyData('RB=F'));
    }

    /**
     * Obtiene el precio de London Gas Oil (ICE) desde el scanner de TradingView (ICEEUR:ULS1!).
     */
    public function fetchGasoilFromTradingView(): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://scanner.tradingview.com/futures/scan', [
                    'symbols' => [
                        'tickers' => ['ICEEUR:ULS1!'],
                    ],
                    'columns' => ['close', 'change', 'change_abs'],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $tickerData = $data['data'][0]['d'] ?? null;

                if ($tickerData && count($tickerData) >= 3) {
                    $price     = (float) $tickerData[0];
                    $changePct = (float) $tickerData[1];
                    $change    = (float) $tickerData[2];

                    return [
                        'symbol'     => 'LGO',
                        'price'      => $price,
                        'change'     => $change,
                        'change_pct' => $changePct,
                        'currency'   => 'USD',
                        'is_up'      => $change >= 0,
                        'updated_at' => now('Europe/Madrid')->format('H:i:s'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('FuelMarketsService: Failed to fetch Gasoil from TradingView: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Llama a Yahoo Finance para actualizar los datos de ambos futuros.
     * Intenta primero el endpoint v7 (quote), y si falla usa v8/chart.
     */
    public function refresh(): void
    {
        // 1. Intentamos obtener el Gasoil Londres directamente de TradingView
        $gasoilPayload = $this->fetchGasoilFromTradingView();
        if ($gasoilPayload) {
            Cache::put(self::GASOIL_CACHE_KEY, $gasoilPayload, self::CACHE_TTL);
            Log::info('FuelMarketsService: Updated London Gasoil via TradingView');
        }

        try {
            // Buscaremos BZ=F (Brent como fallback para Gasoil) y RB=F (RBOB) y EUR=X
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept'          => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Referer'         => 'https://finance.yahoo.com/',
                ])
                ->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                    'symbols' => 'BZ=F,RB=F,EUR=X',
                    'fields'  => 'regularMarketPrice,regularMarketChange,regularMarketChangePercent,regularMarketTime,currency',
                    'lang'    => 'en-US',
                    'region'  => 'US',
                ]);

            if (! $response->successful()) {
                Log::warning('FuelMarketsService: v7 returned ' . $response->status() . '. Falling back to v8/chart.');
                $this->refreshViaChart('EUR=X');
                if (!$gasoilPayload) {
                    $this->refreshViaChart('BZ=F');
                }
                $this->refreshViaChart('RB=F');
                return;
            }

            $quotes = $response->json('quoteResponse.result', []);
            if (empty($quotes)) {
                Log::warning('FuelMarketsService: v7 returned empty result. Falling back to v8/chart.');
                $this->refreshViaChart('EUR=X');
                if (!$gasoilPayload) {
                    $this->refreshViaChart('BZ=F');
                }
                $this->refreshViaChart('RB=F');
                return;
            }

            // Primero buscamos y guardamos el cambio USD/EUR (lo mantenemos por si se usa en otros sitios)
            $rate = 0.92;
            foreach ($quotes as $quote) {
                $symbol = $quote['symbol'] ?? '';
                if ($symbol === 'EUR=X' || $symbol === 'USDEUR=X') {
                    $rate = (float) ($quote['regularMarketPrice'] ?? 0.92);
                    Cache::put('fuel_markets_usd_eur_rate', $rate, self::CACHE_TTL);
                }
            }

            // Ahora aplicamos los datos de los futuros (manteniéndolos en USD)
            foreach ($quotes as $quote) {
                $symbol = $quote['symbol'] ?? '';
                if ($symbol === 'EUR=X' || $symbol === 'USDEUR=X') {
                    continue;
                }

                // Si ya pudimos obtener el Gasoil real de Investing, no lo sobrescribimos con el Brent de Yahoo
                if ($symbol === 'BZ=F' && $gasoilPayload) {
                    continue;
                }

                $price  = (float) ($quote['regularMarketPrice'] ?? 0);
                $change = (float) ($quote['regularMarketChange'] ?? 0);

                if ($symbol === 'RB=F' && $price > 10.0) {
                    $price  = $price / 100.0;
                    $change = $change / 100.0;
                }

                $payload = [
                    'symbol'     => $symbol === 'BZ=F' ? 'BZ=F (Brent)' : $symbol,
                    'price'      => round($price, 4),
                    'change'     => round($change, 4),
                    'change_pct' => round($quote['regularMarketChangePercent'] ?? 0, 2),
                    'currency'   => 'USD',
                    'is_up'      => $change >= 0,
                    'updated_at' => now('Europe/Madrid')->format('H:i:s'),
                ];

                if ($symbol === 'BZ=F') {
                    Cache::put(self::GASOIL_CACHE_KEY, $payload, self::CACHE_TTL);
                } elseif ($symbol === 'RB=F') {
                    Cache::put(self::RBOB_CACHE_KEY, $payload, self::CACHE_TTL);
                }
            }

            Log::info('FuelMarketsService: Updated quotes via v7.');

        } catch (\Throwable $e) {
            Log::warning('FuelMarketsService (v7 exception): ' . $e->getMessage());
            $this->refreshViaChart('EUR=X');
            if (!$gasoilPayload) {
                $this->refreshViaChart('BZ=F');
            }
            $this->refreshViaChart('RB=F');
        }
    }

    /**
     * Fallback: obtiene datos desde el endpoint v8/finance/chart.
     */
    private function refreshViaChart(string $symbol): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                    'Accept'     => 'application/json',
                    'Referer'    => 'https://finance.yahoo.com/',
                ])
                ->get('https://query1.finance.yahoo.com/v8/finance/chart/' . $symbol, [
                    'interval' => '1d',
                    'range'    => '1d',
                ]);

            if (! $response->successful()) {
                Log::warning("FuelMarketsService (chart/{$symbol}): status " . $response->status());
                return;
            }

            $data   = $response->json();
            $result = $data['chart']['result'][0] ?? null;
            if (! $result) return;

            $meta      = $result['meta'];
            $price     = $meta['regularMarketPrice'] ?? 0;
            $prevClose = $meta['previousClose'] ?? $meta['chartPreviousClose'] ?? $price;
            $change    = $price - $prevClose;
            $changePct = $prevClose > 0 ? ($change / $prevClose) * 100 : 0;

            if ($symbol === 'EUR=X' || $symbol === 'USDEUR=X') {
                Cache::put('fuel_markets_usd_eur_rate', $price, self::CACHE_TTL);
                Log::info("FuelMarketsService (chart): Updated USD/EUR rate to {$price}.");
                return;
            }

            if ($symbol === 'RB=F' && $price > 10.0) {
                $price  = $price / 100.0;
                $change = $change / 100.0;
                $changePct = $prevClose > 0 ? ($change / ($prevClose / 100.0)) * 100 : 0;
            }

            $payload = [
                'symbol'     => $symbol === 'BZ=F' ? 'BZ=F (Brent)' : $symbol,
                'price'      => round($price, 4),
                'change'     => round($change, 4),
                'change_pct' => round($changePct, 2),
                'currency'   => 'USD',
                'is_up'      => $change >= 0,
                'updated_at' => now('Europe/Madrid')->format('H:i:s'),
            ];

            if ($symbol === 'BZ=F') {
                Cache::put(self::GASOIL_CACHE_KEY, $payload, self::CACHE_TTL);
            } elseif ($symbol === 'RB=F') {
                Cache::put(self::RBOB_CACHE_KEY, $payload, self::CACHE_TTL);
            }

            Log::info("FuelMarketsService (chart): Updated {$symbol} (USD).");

        } catch (\Throwable $e) {
            Log::warning("FuelMarketsService (chart/{$symbol}): " . $e->getMessage());
        }
    }

    private function emptyData(string $symbol): array
    {
        return [
            'symbol'     => $symbol,
            'price'      => null,
            'change'     => null,
            'change_pct' => null,
            'currency'   => 'USD',
            'is_up'      => null,
            'updated_at' => null,
        ];
    }
}
