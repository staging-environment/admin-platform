<?php

namespace App\Http\Controllers;

use App\Services\AdministracionCorporativaService;
use App\Services\VirtusgesnetService;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
     * Mostrar el dashboard principal
     */
    public function index()
    {
        try {
            $tablesAdmin = $this->adminService->getTables();
            $tablesVirtus = $this->virtusService->getTables();

            return view('dashboard', [
                'tablesAdmin' => $tablesAdmin,
                'tablesVirtus' => $tablesVirtus,
                'user' => auth()->user(),
            ]);
        } catch (\Exception $e) {
            return view('dashboard', [
                'error' => 'Error al conectar con las bases de datos: ' . $e->getMessage(),
                'tablesAdmin' => [],
                'tablesVirtus' => [],
                'user' => auth()->user(),
            ]);
        }
    }
}
