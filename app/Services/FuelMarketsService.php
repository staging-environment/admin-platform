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
     * Llama a Yahoo Finance para actualizar los datos de ambos futuros.
     * Intenta primero el endpoint v7 (quote), y si falla usa v8/chart.
     */
    public function refresh(): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept'          => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Referer'         => 'https://finance.yahoo.com/',
                ])
                ->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                    'symbols' => 'BZ=F,RB=F',
                    'fields'  => 'regularMarketPrice,regularMarketChange,regularMarketChangePercent,regularMarketTime,currency',
                    'lang'    => 'en-US',
                    'region'  => 'US',
                ]);

            if (! $response->successful()) {
                Log::warning('FuelMarketsService: v7 returned ' . $response->status() . '. Falling back to v8/chart.');
                $this->refreshViaChart('BZ=F');
                $this->refreshViaChart('RB=F');
                return;
            }

            $quotes = $response->json('quoteResponse.result', []);
            if (empty($quotes)) {
                Log::warning('FuelMarketsService: v7 returned empty result. Falling back to v8/chart.');
                $this->refreshViaChart('BZ=F');
                $this->refreshViaChart('RB=F');
                return;
            }

            foreach ($quotes as $quote) {
                $symbol = $quote['symbol'] ?? '';
                $change = $quote['regularMarketChange'] ?? 0;

                $payload = [
                    'symbol'     => $symbol,
                    'price'      => round($quote['regularMarketPrice'] ?? 0, 4),
                    'change'     => round($change, 4),
                    'change_pct' => round($quote['regularMarketChangePercent'] ?? 0, 2),
                    'currency'   => $quote['currency'] ?? 'USD',
                    'is_up'      => $change >= 0,
                    'updated_at' => now()->format('H:i:s'),
                ];

                if ($symbol === 'BZ=F') {
                    Cache::put(self::GASOIL_CACHE_KEY, $payload, self::CACHE_TTL);
                } elseif ($symbol === 'RB=F') {
                    Cache::put(self::RBOB_CACHE_KEY, $payload, self::CACHE_TTL);
                }
            }

            Log::info('FuelMarketsService: Updated ' . count($quotes) . ' quotes via v7.');

        } catch (\Throwable $e) {
            Log::warning('FuelMarketsService (v7 exception): ' . $e->getMessage());
            $this->refreshViaChart('BZ=F');
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

            $payload = [
                'symbol'     => $symbol,
                'price'      => round($price, 4),
                'change'     => round($change, 4),
                'change_pct' => round($changePct, 2),
                'currency'   => $meta['currency'] ?? 'USD',
                'is_up'      => $change >= 0,
                'updated_at' => now()->format('H:i:s'),
            ];

            if ($symbol === 'BZ=F') {
                Cache::put(self::GASOIL_CACHE_KEY, $payload, self::CACHE_TTL);
            } elseif ($symbol === 'RB=F') {
                Cache::put(self::RBOB_CACHE_KEY, $payload, self::CACHE_TTL);
            }

            Log::info("FuelMarketsService (chart): Updated {$symbol}.");

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
