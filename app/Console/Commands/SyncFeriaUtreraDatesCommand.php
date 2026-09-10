<?php

namespace App\Console\Commands;

use App\Models\HomeConfig;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncFeriaUtreraDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-feria-utrera-dates {--year= : Año específico a sincronizar (por defecto el año actual)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las fechas oficiales de la Feria de Utrera desde el portal oficial del Ayuntamiento y las guarda en BD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int) ($this->option('year') ?: now()->year);
        $this->info("Iniciando sincronización de fechas de la Feria de Utrera para el año {$year}...");

        $detected = $this->detectFeriaDates($year);

        $this->info("Fechas detectadas:");
        $this->line(" - Inicio: " . $detected['inicio']->toDateTimeString());
        $this->line(" - Fin:    " . $detected['fin']->toDateTimeString());
        $this->line(" - Fuente: " . $detected['source']);
        if (!empty($detected['matched'])) {
            $this->line(" - Coincidencia: " . $detected['matched']);
        }

        try {
            $config = HomeConfig::first();
            if (!$config) {
                $config = new HomeConfig();
            }

            $config->feria_utrera_inicio = $detected['inicio'];
            $config->feria_utrera_fin = $detected['fin'];
            $config->feria_utrera_synced_at = now();
            $config->feria_utrera_source = mb_substr($detected['source'] . (!empty($detected['matched']) ? ' [' . $detected['matched'] . ']' : ''), 0, 255);
            $config->save();

            $msg = "Fechas de la Feria de Utrera {$year} actualizadas correctamente en BD ({$detected['inicio']->format('d/m/Y H:i')} a {$detected['fin']->format('d/m/Y H:i')})";
            $this->info($msg);
            Log::info("SyncFeriaUtreraDates: " . $msg . " desde " . $detected['source']);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $err = "Error al guardar las fechas de la Feria de Utrera en BD: " . $e->getMessage();
            $this->error($err);
            Log::error("SyncFeriaUtreraDates: " . $err);
            return Command::FAILURE;
        }
    }

    /**
     * Consulta las fuentes web del Ayuntamiento de Utrera y detecta las fechas oficiales.
     */
    protected function detectFeriaDates(int $year): array
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        $searchUrls = [
            "https://www.utrera.org/?s=" . urlencode("feria " . $year),
            "https://www.utrera.org/?s=" . urlencode("feria consolacion " . $year),
            "https://www.utrera.org/?s=feria",
        ];

        foreach ($searchUrls as $searchUrl) {
            try {
                $response = Http::withHeaders(['User-Agent' => $userAgent])
                    ->timeout(12)
                    ->get($searchUrl);

                if (!$response->successful()) {
                    continue;
                }

                $dom = new DOMDocument();
                @$dom->loadHTML($response->body());
                $xpath = new DOMXPath($dom);

                $articles = $xpath->query('//article//h2/a | //article//h3/a | //article//a[contains(@class, "entry-title")]');
                $articleLinks = [];
                foreach ($articles as $link) {
                    $href = $link->getAttribute('href');
                    if ($href && !in_array($href, $articleLinks)) {
                        $articleLinks[] = $href;
                    }
                    if (count($articleLinks) >= 6) {
                        break;
                    }
                }

                foreach ($articleLinks as $url) {
                    try {
                        $artRes = Http::withHeaders(['User-Agent' => $userAgent])
                            ->timeout(10)
                            ->get($url);

                        if (!$artRes->successful()) {
                            continue;
                        }

                        $artDom = new DOMDocument();
                        @$artDom->loadHTML($artRes->body());
                        $artXp = new DOMXPath($artDom);
                        $contentNodes = $artXp->query('//div[contains(@class, "entry-content")] | //article');

                        $fullText = '';
                        foreach ($contentNodes as $node) {
                            $fullText .= ' ' . $node->textContent;
                        }
                        $fullText = preg_replace('/\s+/', ' ', $fullText);

                        // Patrón A: "del X al Y de septiembre"
                        if (preg_match('/del\s+(\d{1,2})\s+al\s+(\d{1,2})\s+de\s+septiembre/iu', $fullText, $m)) {
                            $startDay = (int) $m[1];
                            $endDay = (int) $m[2];
                            if ($startDay >= 1 && $startDay <= 8 && $endDay >= $startDay && $endDay <= 12) {
                                return [
                                    'inicio' => Carbon::create($year, 9, $startDay, 20, 0, 0),
                                    'fin' => Carbon::create($year, 9, $endDay, 23, 59, 59),
                                    'source' => $url,
                                    'matched' => $m[0],
                                ];
                            }
                        }

                        // Patrón B: "días 5, 6, 7 y 8 de septiembre"
                        if (preg_match('/días\s+(\d{1,2})(?:[,\s]+(\d{1,2}))*(?:\s+y\s+(\d{1,2}))?\s+de\s+septiembre/iu', $fullText, $m)) {
                            preg_match_all('/\b(\d{1,2})\b/', $m[0], $dayMatches);
                            $days = array_map('intval', $dayMatches[1]);
                            sort($days);
                            if (!empty($days)) {
                                $startDay = $days[0];
                                $endDay = end($days);
                                if ($startDay >= 1 && $startDay <= 8 && $endDay >= $startDay && $endDay <= 12) {
                                    $inicioDay = ($startDay > 4) ? 4 : $startDay;
                                    return [
                                        'inicio' => Carbon::create($year, 9, $inicioDay, 20, 0, 0),
                                        'fin' => Carbon::create($year, 9, $endDay, 23, 59, 59),
                                        'source' => $url,
                                        'matched' => $m[0],
                                    ];
                                }
                            }
                        }

                        // Patrón C: "desde el X de septiembre ... hasta el Y de septiembre"
                        if (preg_match('/desde\s+el\s+(\d{1,2})\s+de\s+septiembre\s+.*?hasta\s+el\s+(\d{1,2})\s+de\s+septiembre/iu', $fullText, $m)) {
                            $startDay = (int) $m[1];
                            $endDay = (int) $m[2];
                            if ($startDay >= 1 && $startDay <= 8 && $endDay >= $startDay && $endDay <= 12) {
                                return [
                                    'inicio' => Carbon::create($year, 9, $startDay, 20, 0, 0),
                                    'fin' => Carbon::create($year, 9, $endDay, 23, 59, 59),
                                    'source' => $url,
                                    'matched' => $m[0],
                                ];
                            }
                        }
                    } catch (\Throwable $e) {
                        // Error leyendo artículo específico, continuar con el siguiente
                    }
                }
            } catch (\Throwable $e) {
                // Error consultando URL de búsqueda, continuar
            }
        }

        // Fallback predeterminado seguro: del 4 (noche) al 8 (medianoche) de septiembre
        return [
            'inicio' => Carbon::create($year, 9, 4, 20, 0, 0),
            'fin' => Carbon::create($year, 9, 8, 23, 59, 59),
            'source' => 'fallback_predeterminado',
            'matched' => '4 al 8 de septiembre (calendario patronal tradicional)',
        ];
    }
}
