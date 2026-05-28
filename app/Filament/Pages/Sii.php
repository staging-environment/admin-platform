<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Sii extends Page
{
    protected string $view = 'filament.pages.sii';

    protected static ?string $title = 'SII';
    protected static ?string $navigationLabel = 'SII';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasRole('Admin');
    }

    public static array $groups = [
        'Facturas y Agrupaciones' => [
            'facturas', 'facturasagrupadasorectificadas', 'detallesdeiva'
        ],
        'Cobros y Pagos' => [
            'cobrospagos', 'cobrosenmetalico'
        ],
        'SII (Envíos y Resultados)' => [
            'resultadosenvios', 'resultadosenvioscobrosmetalico'
        ],
        'Datos Auxiliares y Contabilidad' => [
            'empresas', 'inmuebles', 'mayorescontables', 'festivos', 'fuentesdedatos'
        ]
    ];

    public string $search = '';

    public function getTablesData(): array
    {
        $grouped = [];
        try {
            $allTableNames = [];
            $tablesResult = DB::connection('sii')->select('SHOW TABLES');
            foreach ($tablesResult as $tableRow) {
                $array = (array)$tableRow;
                $allTableNames[] = reset($array);
            }

            // Clean list by matching with search
            $filteredTableNames = $allTableNames;
            if ($this->search !== '') {
                $filteredTableNames = array_filter($allTableNames, function($name) {
                    return stripos($name, $this->search) !== false;
                });
            }

            // Group tables
            $allGroupedTableNames = [];
            foreach (self::$groups as $groupName => $tables) {
                $matchingTables = array_intersect($tables, $filteredTableNames);
                if (!empty($matchingTables)) {
                    $grouped[$groupName] = array_values($matchingTables);
                }
                $allGroupedTableNames = array_merge($allGroupedTableNames, $tables);
            }

            // Handle ungrouped
            $ungrouped = array_diff($filteredTableNames, $allGroupedTableNames);
            if (!empty($ungrouped)) {
                $grouped['Tablas no agrupadas'] = array_values($ungrouped);
            }

        } catch (\Exception $e) {
            // Handle DB connection error or missing tables
            $grouped['Error de conexión: ' . $e->getMessage()] = [];
        }

        return $grouped;
    }
}
