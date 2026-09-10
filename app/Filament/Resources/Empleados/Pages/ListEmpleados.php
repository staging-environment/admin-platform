<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ListEmpleados extends ListRecords
{
    protected static string $resource = EmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn () => $this->exportPdf()),
            CreateAction::make(),
        ];
    }

    public ?string $tableSort = 'apellidos:asc';

    public function mount(): void
    {
        parent::mount();

        if (!$this->tableSort) {
            $this->tableSort = 'apellidos:asc';
        }
    }

    public function exportPdf()
    {
        if (!auth()->user()?->can('gestion_recursos_humanos')) {
            abort(403);
        }

        $query = $this->getFilteredSortedTableQuery();
        $empleados = $query->with(['gasolinera', 'ausencias', 'alertas'])->get();

        $filters = $this->tableFilters ?? [];
        $centroTrabajo = $filters['centro_trabajo']['value'] ?? null;
        $estado = $filters['estado']['value'] ?? null;
        $search = $filters['search']['query'] ?? $this->getTableSearch() ?? null;

        $sortColumn = $this->getTableSortColumn() ?? 'apellidos';
        $sortDirection = $this->getTableSortDirection() ?? 'asc';

        $pdf = Pdf::loadView('pdf.listado-empleados', [
            'empleados' => $empleados,
            'centroTrabajo' => $centroTrabajo,
            'estado' => $estado,
            'search' => $search,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'generatedAt' => Carbon::now()->timezone('Europe/Madrid')->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        $filename = 'Listado_Empleados_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
