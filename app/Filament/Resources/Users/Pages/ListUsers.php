<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Cambiamos esta línea para que apunte a la carpeta Users
use App\Filament\Resources\Users\UserResource;

use App\Filament\Traits\HasMenuBreadcrumbs;
class ListUsers extends ListRecords
{
    use HasMenuBreadcrumbs;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportOnboardingPendingPdf')
                ->label('Informe Onboarding Pendiente')
                ->icon('heroicon-o-document-chart-bar')
                ->color('warning')
                ->size(\Filament\Support\Enums\Size::ExtraSmall)
                ->extraAttributes([
                    'style' => 'padding: 0.25rem 0.65rem !important; font-size: 0.75rem !important; min-height: 28px !important; height: 28px !important;',
                ])
                ->action(function (\App\Services\OnboardingReportService $reportService) {
                    $pdf = $reportService->generatePdf();
                    $filename = 'Informe_Onboarding_Pendiente_' . \Carbon\Carbon::now()->format('Ymd_His') . '.pdf';
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename, [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
            Actions\CreateAction::make()
                ->color('success')
                ->extraAttributes([
                    'style' => 'background-color: #16a34a !important; color: #ffffff !important; border-color: #16a34a !important;',
                ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Users\Widgets\UsersInfoWidget::class,
        ];
    }
}
