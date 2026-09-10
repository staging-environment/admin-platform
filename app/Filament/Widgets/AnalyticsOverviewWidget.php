<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Last 30 days stats (only public portal)
        $totalViews = PageView::publicPortal()->where('created_at', '>=', now()->subDays(30))->count();
        $uniqueVisitors = PageView::publicPortal()->where('created_at', '>=', now()->subDays(30))
            ->distinct('ip_address')
            ->count('ip_address');
            
        // Calculate average views per visitor and daily average
        $avgViews = $uniqueVisitors > 0 ? round($totalViews / $uniqueVisitors, 2) : 0;
        $dailyAvg = round($totalViews / 30, 0);

        return [
            Stat::make('Páginas Vistas (30d)', number_format($totalViews))
                ->description('Total de páginas públicas cargadas')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Visitantes Únicos (30d)', number_format($uniqueVisitors))
                ->description('Usuarios únicos (basado en IP diaria)')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Páginas por Visitante (30d)', $avgViews)
                ->description("Promedio en 30 días (~{$dailyAvg} págs/día)")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
