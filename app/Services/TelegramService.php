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
        $this->token = config('services.telegram.bot_token');
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
        Log::info('TelegramService: Incoming update: ' . json_encode($update));

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

        // Search for the user having this mobile number
        // We compare the clean numbers to avoid formatting issues
        $user = \App\Models\User::all()->first(function ($u) use ($baseNumber) {
            $p = preg_replace('/\D/', '', $u->telefono);
            
            // Remove 34 prefix from database fields too, just in case
            if (str_starts_with($p, '34') && strlen($p) === 11) $p = substr($p, 2);

            return $p === $baseNumber;
        });

        if ($user) {
            // Check if another user already has this chat id
            \App\Models\User::where('telegram_chat_id', $chatId)
                ->where('id', '!=', $user->id)
                ->update(['telegram_chat_id' => null]);

            // Save the telegram chat id
            $user->telegram_chat_id = $chatId;
            $user->save();

            $this->sendMessage($chatId, "✅ <b>¡Vinculación completada con éxito!</b>\n\n" .
                                         "Se ha asociado tu cuenta de Telegram al usuario: <b>{$user->name}</b>.\n\n" .
                                         "Recibirás alertas inmediatas cuando detectemos cambios de precios de la competencia en tus localidades.");
        } else {
            $this->sendMessage($chatId, "❌ El número de teléfono <b>+{$phone}</b> no está registrado en nuestra base de datos de usuarios.\n\n" .
                                         "Por favor, asegúrate de que tu perfil de usuario tenga configurado este número de teléfono.");
        }
    }
}
