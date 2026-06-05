<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function mount()
    {
        $user = auth()->user();
        if ($user && ($user->hasRole('Gestor') || $user->hasRole('gestor'))) {
            return redirect()->to('/admin/recursos-humanos');
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
