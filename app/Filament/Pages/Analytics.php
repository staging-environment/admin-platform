<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\AnalyticsOverviewWidget;
use App\Filament\Widgets\PageViewChartWidget;
use App\Filament\Widgets\MostVisitedPagesWidget;

class Analytics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Analítica';
    protected static ?string $title = 'Estadísticas de Visitas';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected string $view = 'filament.pages.analytics';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Reload relationships to bypass session/memory stale data
        $user->load('roles', 'permissions');
        
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        
        return $user->hasRole('Admin') || $user->can('ver_analiticas');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsOverviewWidget::class,
            PageViewChartWidget::class,
            MostVisitedPagesWidget::class,
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Administración',
            static::getNavigationLabel(),
        ];
    }
}
