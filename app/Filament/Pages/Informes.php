<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Informes extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración de la plataforma';

    protected static ?string $title = 'Informes';

    protected string $view = 'filament.pages.informes';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        return method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('super-admin'))
            ? true
            : parent::canAccess();
    }

    protected function getViewData(): array
    {
        $tables = [];
        try {
            $virtusgesnetService = app(\App\Services\VirtusgesnetService::class);
            $tables = $virtusgesnetService->getTables();
        } catch (\Throwable $e) {}

        return [
            'tableGroups' => $this->groupTablesByBusinessArea($tables),
        ];
    }

    private function groupTablesByBusinessArea(array $tables): array
    {
        return [
            'ventas' => $this->filterTables($tables, [
                'deventa', 'yticketsdeventa', 'ventasencurso', 'ticketsdelavado', 'ventaexclusiva',
            ]),
            'compras' => $this->filterTables($tables, [
                'decompra', 'proveedor', 'proveedores', 'pedidosdecompra',
            ]),
        ];
    }

    private function filterTables(array $tables, array $keywords): array
    {
        return array_values(array_filter($tables, function (string $table) use ($keywords) {
            $normalizedTable = mb_strtolower($table);
            foreach ($keywords as $keyword) {
                if (str_contains($normalizedTable, mb_strtolower($keyword))) {
                    return true;
                }
            }
            return false;
        }));
    }
}
