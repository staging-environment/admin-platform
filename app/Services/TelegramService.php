<?php

namespace App\Services;

use App\Models\Empleado;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $token;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Send a text message to a specific Chat ID.
     */
    public function sendMessage(string $chatId, string $text): bool
    {
        if (empty($this->token)) {
            Log::warning('TelegramService: TELEGRAM_BOT_TOKEN is not configured.');
            return false;
        }

        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('TelegramService Error sending message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle incoming bot updates (webhook or polling).
     */
    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? null;
        if (!$message) return;

        $chatId = $message['chat']['id'] ?? null;
        if (!$chatId) return;

        // 1. Process contact sharing
        if (isset($message['contact'])) {
            $this->handleContactSharing($chatId, $message['contact']);
            return;
        }

        // 2. Process commands
        $text = trim($message['text'] ?? '');
        if (str_starts_with($text, '/start')) {
            $this->sendStartWelcome($chatId);
            return;
        }

        // 3. Fallback message
        $this->sendMessage($chatId, "Por favor, utiliza el botón <b>📱 Compartir Teléfono</b> de abajo para registrar tu número en el sistema y activar las alertas.");
    }

    /**
     * Sends welcome message asking for contact.
     */
    protected function sendStartWelcome(string $chatId): void
    {
        if (empty($this->token)) return;

        $text = "¡Hola! Bienvenido al sistema de alertas de competencia de Utrecar.\n\n" .
                "Para recibir avisos de cambios de precios directamente en tu móvil, necesitamos verificar tu número de teléfono.\n\n" .
                "Por favor, presiona el botón <b>📱 Compartir Teléfono</b> de abajo.";

        $keyboard = [
            'keyboard' => [
                [
                    [
                        'text' => '📱 Compartir Teléfono',
                        'request_contact' => true
                    ]
                ]
            ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true
        ];

        Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Handles contact payload shared by the user.
     */
    protected function handleContactSharing(string $chatId, array $contact): void
    {
        $phone = $contact['phone_number'] ?? '';
        if (empty($phone)) {
            $this->sendMessage($chatId, "No se pudo obtener el número de teléfono.");
            return;
        }

        // Normalize phone number (keep only digits)
        $cleanPhone = preg_replace('/\D/', '', $phone);
        
        // Match Spanish country code prefix (+34 / 34) and extract the base 9-digit mobile number
        $baseNumber = $cleanPhone;
        if (str_starts_with($cleanPhone, '34') && strlen($cleanPhone) === 11) {
            $baseNumber = substr($cleanPhone, 2);
        }

        if (strlen($baseNumber) !== 9) {
            $this->sendMessage($chatId, "Lo siento, el número de teléfono <b>+{$phone}</b> no parece ser un móvil válido de España (9 dígitos).");
            return;
        }

        // Search for the employee having this mobile number
        // We compare the clean numbers to avoid spaces/dashes formatting issues
        $empleado = Empleado::all()->first(function ($emp) use ($baseNumber) {
            $p1 = preg_replace('/\D/', '', $emp->telefono_principal);
            $p2 = preg_replace('/\D/', '', $emp->telefono_secundario);
            
            // Remove 34 prefix from database fields too, just in case
            if (str_starts_with($p1, '34') && strlen($p1) === 11) $p1 = substr($p1, 2);
            if (str_starts_with($p2, '34') && strlen($p2) === 11) $p2 = substr($p2, 2);

            return $p1 === $baseNumber || $p2 === $baseNumber;
        });

        if ($empleado) {
            // Check if another employee already has this chat id
            Empleado::where('telegram_chat_id', $chatId)
                ->where('id', '!=', $empleado->id)
                ->update(['telegram_chat_id' => null]);

            // Save the telegram chat id
            $empleado->telegram_chat_id = $chatId;
            $empleado->save();

            $this->sendMessage($chatId, "✅ <b>¡Vinculación completada con éxito!</b>\n\n" .
                                         "Se ha asociado tu cuenta de Telegram al empleado: <b>{$empleado->nombre} {$empleado->apellidos}</b>.\n\n" .
                                         "Recibirás alertas inmediatas cuando detectemos cambios de precios de la competencia en tus localidades.");
        } else {
            $this->sendMessage($chatId, "❌ El número de teléfono <b>+{$phone}</b> no está registrado en nuestra base de datos de empleados.\n\n" .
                                         "Por favor, asegúrate de que tu ficha de empleado tenga configurado este número como teléfono principal.");
        }
    }
}
