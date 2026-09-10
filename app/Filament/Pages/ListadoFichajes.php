<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\EmpleadoFichaje;
use Carbon\Carbon;
use Livewire\WithPagination;

class ListadoFichajes extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Listado de Fichajes';
    protected static ?string $title = 'Control General de Fichajes';
    protected static ?string $slug = 'fichajes';
    protected static string|\UnitEnum|null $navigationGroup = 'Recursos humanos';

    public function getBreadcrumbs(): array
    {
        return [
            'Recursos humanos',
            'Fichajes',
            'Listado',
        ];
    }

    protected string $view = 'filament.pages.listado-fichajes';

    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterSearch = '';
    public $sortField = 'fecha';
    public $sortDirection = 'desc';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->can('ver_listado_fichajes')
            || $user->email === 'jarodriguezbonilla@gmail.com'
            || $user->id === 1;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = ($field === 'fecha') ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function updatingFilterSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function getTodosLosFichajesQuery()
    {
        $search = $this->filterSearch ? '%' . $this->filterSearch . '%' : null;

        $query = EmpleadoFichaje::with(['empleado.gasolinera'])
            ->leftJoin('empleados', 'empleado_fichajes.empleado_id', '=', 'empleados.id')
            ->select('empleado_fichajes.*');

        if ($this->filterDateFrom) {
            $query->where('empleado_fichajes.fecha', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->where('empleado_fichajes.fecha', '<=', $this->filterDateTo);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('empleados.nombre', 'like', $search)
                  ->orWhere('empleados.apellidos', 'like', $search)
                  ->orWhere('empleados.email', 'like', $search);
            });
        }

        if ($this->sortField === 'nombre') {
            $query->orderBy('empleados.nombre', $this->sortDirection)
                  ->orderBy('empleados.apellidos', $this->sortDirection)
                  ->orderBy('empleado_fichajes.fecha', 'desc')
                  ->orderBy('empleado_fichajes.hora_entrada', 'desc');
        } elseif ($this->sortField === 'fecha') {
            $query->orderBy('empleado_fichajes.fecha', $this->sortDirection)
                  ->orderBy('empleado_fichajes.hora_entrada', $this->sortDirection)
                  ->orderBy('empleados.apellidos', 'asc');
        } else {
            $query->orderBy('empleados.apellidos', $this->sortDirection)
                  ->orderBy('empleados.nombre', $this->sortDirection)
                  ->orderBy('empleado_fichajes.fecha', 'desc')
                  ->orderBy('empleado_fichajes.hora_entrada', 'desc');
        }

        return $query;
    }

    protected function getViewData(): array
    {
        return [
            'todosLosFichajes' => $this->getTodosLosFichajesQuery()->paginate(50),
        ];
    }

    public function exportPdf()
    {
        $fichajes = $this->getTodosLosFichajesQuery()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.control-general-fichajes', [
            'fichajes' => $fichajes,
            'filterDateFrom' => $this->filterDateFrom,
            'filterDateTo' => $this->filterDateTo,
            'filterSearch' => $this->filterSearch,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'generatedAt' => Carbon::now()->timezone('Europe/Madrid')->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        $filename = 'Control_General_Fichajes_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
