<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listar filtros del usuario actual
     * GET /api/filters
     */
    public function index()
    {
        try {
            $filters = auth()->user()->filters()->get();

            return response()->json([
                'success' => true,
                'data' => $filters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un nuevo filtro
     * POST /api/filters
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'database' => 'required|in:administracioncorporativa,virtusgesnet',
                'table_name' => 'required|string|max:100',
                'filter_criteria' => 'nullable|array',
                'limit' => 'nullable|integer|min:1|max:500',
            ]);

            $filter = auth()->user()->filters()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'database' => $validated['database'],
                'table_name' => $validated['table_name'],
                'filter_criteria' => $validated['filter_criteria'] ?? [],
                'limit' => $validated['limit'] ?? 100,
            ]);

            return response()->json([
                'success' => true,
                'data' => $filter,
                'message' => 'Filtro creado exitosamente',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener un filtro específico
     * GET /api/filters/{id}
     */
    public function show($id)
    {
        try {
            $filter = auth()->user()->filters()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $filter,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filtro no encontrado',
            ], 404);
        }
    }

    /**
     * Actualizar un filtro
     * PUT /api/filters/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $filter = auth()->user()->filters()->findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string|max:1000',
                'filter_criteria' => 'nullable|array',
                'limit' => 'nullable|integer|min:1|max:500',
            ]);

            $filter->update($validated);

            return response()->json([
                'success' => true,
                'data' => $filter,
                'message' => 'Filtro actualizado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Eliminar un filtro
     * DELETE /api/filters/{id}
     */
    public function destroy($id)
    {
        try {
            $filter = auth()->user()->filters()->findOrFail($id);
            $filter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Filtro eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}


