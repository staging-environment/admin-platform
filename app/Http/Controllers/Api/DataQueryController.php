<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdministracionCorporativaService;
use App\Services\VirtusgesnetService;
use Illuminate\Http\Request;

class DataQueryController extends Controller
{
    protected $adminService;
    protected $virtusService;

    public function __construct(
        AdministracionCorporativaService $adminService,
        VirtusgesnetService $virtusService
    ) {
        $this->adminService = $adminService;
        $this->virtusService = $virtusService;
    }

    /**
     * Obtener tablas disponibles
     * GET /api/databases/tables
     */
    public function getTables(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'administracioncorporativa' => $this->adminService->getTables(),
                    'virtusgesnet' => $this->virtusService->getTables(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener datos de una tabla
     * POST /api/data/query
     */
    public function query(Request $request)
    {
        try {
            $validated = $request->validate([
                'database' => 'required|in:administracioncorporativa,virtusgesnet',
                'table' => 'required|string|max:100',
                'filters' => 'nullable|array',
                'limit' => 'nullable|integer|min:1|max:500',
                'offset' => 'nullable|integer|min:0',
            ]);

            $database = $validated['database'];
            $table = $validated['table'];
            $filters = $validated['filters'] ?? [];
            $limit = $validated['limit'] ?? 100;
            $offset = $validated['offset'] ?? 0;

            if ($database === 'administracioncorporativa') {
                $result = $this->adminService->getTableData($table, $filters, $limit, $offset);
            } else {
                $result = $this->virtusService->getTableData($table, $filters, $limit, $offset);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Ejecutar consulta SELECT personalizada
     * POST /api/data/custom-query
     */
    public function customQuery(Request $request)
    {
        try {
            $validated = $request->validate([
                'database' => 'required|in:administracioncorporativa,virtusgesnet',
                'sql' => 'required|string',
            ]);

            $database = $validated['database'];
            $sql = $validated['sql'];

            if ($database === 'administracioncorporativa') {
                $result = $this->adminService->query($sql);
            } else {
                $result = $this->virtusService->query($sql);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener esquema de una tabla
     * GET /api/data/schema
     */
    public function getSchema(Request $request)
    {
        try {
            $validated = $request->validate([
                'database' => 'required|in:administracioncorporativa,virtusgesnet',
                'table' => 'required|string|max:100',
            ]);

            $database = $validated['database'];
            $table = $validated['table'];

            if ($database === 'administracioncorporativa') {
                $schema = $this->adminService->getTableSchema($table);
            } else {
                $schema = $this->virtusService->getTableSchema($table);
            }

            return response()->json([
                'success' => true,
                'data' => $schema,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

