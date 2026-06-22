<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Last 30 days stats
        $totalViews = PageView::where('created_at', '>=', now()->subDays(30))->count();
        $uniqueVisitors = PageView::where('created_at', '>=', now()->subDays(30))
            ->distinct('ip_address')
            ->count('ip_address');
            
        // Calculate average views per visitor
        $avgViews = $uniqueVisitors > 0 ? round($totalViews / $uniqueVisitors, 2) : 0;

        return [
            Stat::make('Páginas Vistas (30d)', number_format($totalViews))
                ->description('Total de páginas cargadas por los usuarios')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Visitantes Únicos (30d)', number_format($uniqueVisitors))
                ->description('Usuarios únicos (basado en IP diaria)')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Visitas por Usuario', $avgViews)
                ->description('Promedio de páginas vistas por visitante')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
