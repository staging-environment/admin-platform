<?php

namespace App\Filament\Widgets;

use App\Models\ContactoMensaje;
use Filament\Widgets\Widget;

class UnreadContactMessagesWidget extends Widget
{
    protected string $view = 'filament.widgets.unread-contact-messages-widget';

    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 'full';

    public function getUnreadMessages()
    {
        return ContactoMensaje::with('gasolinera')
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_dashboard');
    }
}
