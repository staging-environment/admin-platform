<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('Admin')) {
                if ($user->can('ver_dashboard')) {
                    return redirect()->to('/admin/dashboard');
                }
                if ($user->can('gestion_recursos_humanos')) {
                    return redirect()->to('/admin/recursos-humanos');
                }
            }

            if ($user->hasRole('Gestor') || $user->hasRole('gestor')) {
                if ($user->can('gestion_recursos_humanos')) {
                    return redirect()->to('/admin/recursos-humanos');
                }
                if ($user->can('ver_dashboard')) {
                    return redirect()->to('/admin/dashboard');
                }
                }
        }
        return redirect()->to('/admin/dashboard');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_dashboard');
    }
}
