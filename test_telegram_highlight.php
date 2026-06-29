<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MineturService;
use App\Services\TelegramService;
use App\Models\User;
use Spatie\Permission\Models\Permission;

// Setup mock / test data
$oldDiesel = [
    ['id' => 1, 'name' => 'ES VISTALEGRE', 'price' => 1.429],
    ['id' => 2, 'name' => 'RONDA NORTE', 'price' => 1.459],
    ['id' => 3, 'name' => 'RODALABOTA', 'price' => 1.399],
    ['id' => 4, 'name' => 'ATENAS', 'price' => 1.409],
    ['id' => 5, 'name' => 'ESTACION 5', 'price' => 1.489],
];

$newDiesel = [
    ['id' => 3, 'name' => 'RODALABOTA', 'price' => 1.399],
    ['id' => 4, 'name' => 'ATENAS', 'price' => 1.409],
    ['id' => 1, 'name' => 'ES VISTALEGRE', 'price' => 1.419], // Price changed! (1.429 -> 1.419) - bajó
    ['id' => 2, 'name' => 'RONDA NORTE', 'price' => 1.469],  // Price changed! (1.459 -> 1.469) - subió
    ['id' => 5, 'name' => 'ESTACION 5', 'price' => 1.489],
];

$oldGas95 = [
    ['id' => 1, 'name' => 'ES VISTALEGRE', 'price' => 1.559],
    ['id' => 2, 'name' => 'RONDA NORTE', 'price' => 1.569],
    ['id' => 3, 'name' => 'RODALABOTA', 'price' => 1.579],
    ['id' => 4, 'name' => 'ATENAS', 'price' => 1.589],
    ['id' => 5, 'name' => 'ESTACION 5', 'price' => 1.599],
];

$newGas95 = [
    ['id' => 1, 'name' => 'ES VISTALEGRE', 'price' => 1.559],
    ['id' => 2, 'name' => 'RONDA NORTE', 'price' => 1.549], // Price changed! (1.569 -> 1.549) - bajó
    ['id' => 3, 'name' => 'RODALABOTA', 'price' => 1.579],
    ['id' => 4, 'name' => 'ATENAS', 'price' => 1.589],
    ['id' => 5, 'name' => 'ESTACION 5', 'price' => 1.599],
];

// Mock TelegramService so we can intercept and print the messages
$mockTelegram = Mockery::mock(TelegramService::class);
$mockTelegram->shouldReceive('sendMessage')->andReturnUsing(function($chatId, $text) {
    echo "=== MOCK TELEGRAM MESSAGE ===\n";
    echo $text . "\n";
    echo "=============================\n\n";
    return true;
});
$app->instance(TelegramService::class, $mockTelegram);

// Create database records for the test
try {
    $permission = Permission::firstOrCreate(['name' => 'recibir_notificaciones_competencia']);
    $user = User::firstOrCreate(
        ['email' => 'test-competencia@example.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]
    );
    $user->telegram_chat_id = '123456789';
    $user->save();
    if (!$user->hasPermissionTo($permission)) {
        $user->givePermissionTo($permission);
    }
} catch (\Throwable $e) {
    echo "DB setup error: " . $e->getMessage() . "\n";
}

$service = new MineturService();

echo "--- TESTING DIESEL CHANGE NOTIFICATION ---\n";
$service->notifyChanges('utrera', 'diesel', $newDiesel, $oldDiesel);

echo "--- TESTING GAS95 CHANGE NOTIFICATION ---\n";
$service->notifyChanges('utrera', 'gas95', $newGas95, $oldGas95);

// Clean up DB records
try {
    if (isset($user)) {
        $user->delete();
    }
} catch (\Throwable $e) {
    // Ignore cleanup errors
}
