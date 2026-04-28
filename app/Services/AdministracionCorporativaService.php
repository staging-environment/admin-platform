<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdministracionCorporativaService
{
    /**
     * Obtener todas las tablas de la base de datos administracioncorporativa
     */

   public function getTables()
   {
       $database = config('database.connections.administracioncorporativa.database');
       $tables = DB::connection('administracioncorporativa')
           ->select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', [$database]);

       return array_map(function($table) { return $table->TABLE_NAME; }, $tables);
   }

    /**
     * Obtener datos de una tabla con filtros
     */
    public function getTableData($table, $filters = [], $limit = 100, $offset = 0)
    {
        $query = DB::connection('administracioncorporativa')->table($table);

        // Aplicar filtros
        foreach ($filters as $column => $value) {
            if (is_array($value)) {
                // Búsqueda con LIKE
                $query->where($column, 'LIKE', "%{$value['search']}%");
            } else {
                // Búsqueda exacta
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
      * Obtener el esquema de una tabla
      */
     public function getTableSchema($table)
     {
         // Sanitizar el nombre de la tabla para evitar SQL injection
         $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
         return DB::connection('administracioncorporativa')
             ->select('DESCRIBE `' . $table . '`');
     }

    /**
     * Ejecutar una consulta SELECT personalizada
     */
    public function query($sql)
    {
        // Validar que es una consulta SELECT
        if (!preg_match('/^\s*SELECT\s+/i', trim($sql))) {
            throw new \Exception('Solo consultas SELECT están permitidas');
        }

        return DB::connection('administracioncorporativa')->select($sql);
    }
}
