<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VirtusgesnetService
{
    /**
     * Obtener todas las tablas de la base de datos virtusgesnet.
     */
    public function getTables(): array
    {
        $database = config('database.connections.virtusgesnet.database');

        $tables = DB::connection('virtusgesnet')
            ->select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', [$database]);

        return array_map(function ($table) {
            return $table->TABLE_NAME;
        }, $tables);
    }

    /**
     * Obtener datos de una tabla con filtros.
     */
    public function getTableData($table, $filters = [], $limit = 100, $offset = 0): array
    {
        $query = DB::connection('virtusgesnet')->table($table);

        foreach ($filters as $column => $value) {
            if (is_array($value)) {
                $query->where($column, 'LIKE', "%{$value['search']}%");
            } else {
                $query->where($column, $value);
            }
        }

        $total = $query->count();

        $data = $query->limit($limit)
            ->offset($offset)
            ->get();

        return [
            'total' => $total,
            'data' => $data,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Obtener el esquema de una tabla.
     */
    public function getTableSchema($table): array
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        return DB::connection('virtusgesnet')
            ->select('DESCRIBE `' . $table . '`');
    }

    /**
     * Ejecutar una consulta SELECT personalizada.
     */
    public function query($sql): array
    {
        if (!preg_match('/^\s*SELECT\s+/i', trim($sql))) {
            throw new \Exception('Solo consultas SELECT están permitidas');
        }

        return DB::connection('virtusgesnet')->select($sql);
    }

    /**
     * Obtener resumen mensual de ventas desde facturas y tickets de venta.
     */
    public function getMonthlySalesSummary(
        ?int $year = null,
        string $documentType = 'all',
        ?int $startMonth = null,
        ?int $endMonth = null
    ): array {
        $year ??= (int) date('Y');

        $startMonth = $startMonth !== null ? max(1, min(12, $startMonth)) : null;
        $endMonth = $endMonth !== null ? max(1, min(12, $endMonth)) : null;

        if ($startMonth !== null && $endMonth !== null && $startMonth > $endMonth) {
            [$startMonth, $endMonth] = [$endMonth, $startMonth];
        }

        $query = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->selectRaw('
                YEAR(FechaYHora) as year,
                MONTH(FechaYHora) as month,
                COUNT(*) as documents_count,
                SUM(ImporteBruto) as gross_amount,
                SUM(ImporteDeDescuentosYCargos) as discounts_and_charges_amount,
                SUM(ImporteDeIVA) as tax_amount,
                SUM(ImporteTotal) as total_amount
            ')
            ->whereYear('FechaYHora', $year);

        if ($documentType === 'invoices') {
            $query->where('EsTicket', 0);
        }

        if ($documentType === 'tickets') {
            $query->where('EsTicket', 1);
        }

        if ($startMonth !== null) {
            $query->whereRaw('MONTH(FechaYHora) >= ?', [$startMonth]);
        }

        if ($endMonth !== null) {
            $query->whereRaw('MONTH(FechaYHora) <= ?', [$endMonth]);
        }

        return $query
            ->groupByRaw('YEAR(FechaYHora), MONTH(FechaYHora)')
            ->orderByRaw('YEAR(FechaYHora), MONTH(FechaYHora)')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => (int) $row->year,
                    'month' => (int) $row->month,
                    'month_name' => str_pad((string) $row->month, 2, '0', STR_PAD_LEFT) . '/' . $row->year,
                    'documents_count' => (int) $row->documents_count,
                    'gross_amount' => (float) $row->gross_amount,
                    'discounts_and_charges_amount' => (float) $row->discounts_and_charges_amount,
                    'tax_amount' => (float) $row->tax_amount,
                    'total_amount' => (float) $row->total_amount,
                ];
            })
            ->all();
    }
}
