<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TelegramSetupWidget extends Widget
{
    protected static string $view = 'filament.widgets.telegram-setup-widget';

    protected static ?int $sort = -10; // Show at the very top of the dashboard

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Display only if the user has the alert permission but has not linked Telegram yet
        return $user->can('recibir_notificaciones_competencia') && empty($user->telegram_chat_id);
    }
}
