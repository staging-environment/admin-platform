<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$telegramService = app(\App\Services\TelegramService::class);
$users = \App\Models\User::whereNotNull('telegram_chat_id')->get();

$currentPrices = [
    1 => ['name' => 'E.S. VISTALEGRE (Utrera)', 'goa' => 1.359, 'g95e5' => 1.499],
    2 => ['name' => 'RONDA NORTE (Sevilla)', 'goa' => 1.345, 'g95e5' => 1.479],
    3 => ['name' => 'E.S. RODALABOTA (El Cuervo)', 'goa' => 1.379, 'g95e5' => 1.519],
    4 => ['name' => 'E.S. ATENAS (Lebrija)', 'goa' => 1.369, 'g95e5' => 1.509]
];

$lastPrices = [
    1 => ['goa' => 1.349, 'g95e5' => 1.499],
    2 => ['goa' => 1.345, 'g95e5' => 1.489],
    3 => ['goa' => 1.379, 'g95e5' => 1.519],
    4 => ['goa' => 1.359, 'g95e5' => 1.509]
];

if ($users->isNotEmpty()) {
    $text = "📢 <b>[PRUEBA] Nuevos precios actualizados (Utrecar)</b>\n";
    $text .= "Se han enviado correctamente los nuevos precios a MITECO:\n\n";

    foreach ($currentPrices as $stationCode => $prices) {
        $oldGoa = $lastPrices[$stationCode]['goa'] ?? null;
        $oldG95 = $lastPrices[$stationCode]['g95e5'] ?? null;

        $text .= "⛽ <b>" . $prices['name'] . "</b>\n";
        
        // Diésel
        if ($prices['goa'] !== null) {
            $priceText = "  • Diésel A: <b>" . number_format($prices['goa'], 3) . " €</b>";
            if ($oldGoa !== null && abs($oldGoa - $prices['goa']) > 0.0001) {
                $diff = $prices['goa'] - $oldGoa;
                $diffText = ($diff > 0 ? "+" : "") . number_format($diff, 3);
                $priceText .= " ⚠️ <i>(antes " . number_format($oldGoa, 3) . " € | {$diffText} €)</i>";
            }
            $text .= $priceText . "\n";
        }
        
        // Gasolina 95
        if ($prices['g95e5'] !== null) {
            $priceText = "  • Gasolina 95: <b>" . number_format($prices['g95e5'], 3) . " €</b>";
            if ($oldG95 !== null && abs($oldG95 - $prices['g95e5']) > 0.0001) {
                $diff = $prices['g95e5'] - $oldG95;
                $diffText = ($diff > 0 ? "+" : "") . number_format($diff, 3);
                $priceText .= " ⚠️ <i>(antes " . number_format($oldG95, 3) . " € | {$diffText} €)</i>";
            }
            $text .= $priceText . "\n";
        }
        $text .= "\n";
    }

    foreach ($users as $user) {
        $telegramService->sendMessage($user->telegram_chat_id, $text);
    }
}
