<?php

namespace App\Providers\Filament;

use App\Filament\Resources\JobOffers\JobOfferResource; // <-- Importamos tu recurso manualmente
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(fn () => view('components.logo'))
            ->favicon('/ronda_norte_logo.svg')
            ->topNavigation()
            ->topbar(false)
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Red,
                'success' => Color::Green,
            ])
            // Devolvemos el discoverResources a su estado original sin inventos
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            
            // Registramos tu recurso de Ofertas de Empleo y Vacaciones a mano
            ->resources([
                JobOfferResource::class,
                JobApplicationResource::class,
                EmpleadoResource::class,
                \App\Filament\Resources\EmpleadoVacacions\EmpleadoVacacionResource::class,
            ])
            
            ->pages([])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\RedirectToDefaultPanelPage::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                function (): string {
                    $css = \Illuminate\Support\Facades\Blade::render("
                        @vite(['resources/css/filament-nav.css'])
                        @include('filament.custom-filepond')
                        @include('partials.pwa-tags')
                    ");

                    // Estilos globales de tipografía compacta, márgenes y migas de pan para todas las secciones
                    $css .= "
                    <style>
                        /* 1. Ocultar el título principal de página en todas las secciones */
                        .fi-header-heading,
                        h1.fi-header-heading,
                        .fi-page-header .fi-header-heading,
                        .fi-header h1 {
                            display: none !important;
                        }

                        /* 2. Unificación total del margen superior y espaciado de layout en todas las secciones */
                        body.fi-body .fi-layout,
                        body.fi-body .fi-main-ctn,
                        body.fi-body main.fi-main,
                        body.fi-body .fi-content,
                        body.fi-body .fi-page {
                            padding-top: 0px !important;
                            margin-top: 0px !important;
                        }

                        body.fi-body .fi-layout {
                            display: flex !important;
                            flex-direction: column !important;
                            gap: 0px !important;
                            min-height: 0px !important;
                        }

                        body.fi-body .fi-main-ctn {
                            display: flex !important;
                            flex-direction: column !important;
                            gap: 0px !important;
                            margin: 0px !important;
                            padding: 0px !important;
                            flex-grow: 1 !important;
                        }

                        body.fi-body .fi-page-header-main-ctn {
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            padding-block: 0px !important;
                            padding-block-start: 0px !important;
                            padding-block-end: 0px !important;
                            margin-top: 0px !important;
                            margin-bottom: 0px !important;
                            gap: 0px !important;
                            row-gap: 0px !important;
                        }

                        /* 3. Margen unificado y equilibrado de la cabecera / migas de pan en todas las secciones */
                        body.fi-body .fi-header,
                        .fi-header {
                            margin-top: 0.85rem !important;
                            margin-bottom: 0.65rem !important;
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            padding-block: 0px !important;
                            padding-block-start: 0px !important;
                            padding-block-end: 0px !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: space-between !important;
                            gap: 0.5rem !important;
                        }

                        body.fi-body .fi-page-dashboard .fi-header,
                        body.fi-body .fi-dashboard-page .fi-header {
                            display: flex !important;
                            margin-top: 0.85rem !important;
                            margin-bottom: 0.65rem !important;
                        }

                        
                        /* Botones de acción compactos en cabecera para mantener altura homogénea con las migas de pan */
                        .fi-header-actions,
                        .fi-page-header-actions {
                            display: flex !important;
                            align-items: center !important;
                            gap: 0.35rem !important;
                        }

                        .fi-header-actions .fi-btn,
                        .fi-page-header-actions .fi-btn {
                            padding-top: 0.25rem !important;
                            padding-bottom: 0.25rem !important;
                            padding-block: 0.25rem !important;
                            padding-inline: 0.65rem !important;
                            font-size: 0.75rem !important;
                            line-height: 1.25 !important;
                            min-height: 28px !important;
                            height: 28px !important;
                            border-radius: 0.5rem !important;
                        }

                        .fi-header-actions .fi-btn .fi-btn-label,
                        .fi-page-header-actions .fi-btn .fi-btn-label {
                            font-size: 0.75rem !important;
                            font-weight: 600 !important;
                        }

                        .fi-header-actions .fi-btn .fi-btn-icon,
                        .fi-header-actions .fi-btn svg,
                        .fi-page-header-actions .fi-btn svg {
                            width: 0.85rem !important;
                            height: 0.85rem !important;
                        }

                        /* 4. Títulos secundarios reducidos en todas las secciones */
                        .fi-section-header-heading,
                        .fi-sc-section-header-heading,
                        .fi-ta-header-heading,
                        .fi-ta-header-title,
                        .fi-header-subheading,
                        .fi-section-header-title,
                        .fi-section h3,
                        .fi-section h2,
                        .fi-ta-content h3 {
                            font-size: 0.95rem !important; /* ~15px */
                            line-height: 1.3 !important;
                            font-weight: 700 !important;
                        }

                        /* 5. Compactación global de tarjetas (cards), esquemas y secciones en todas las páginas */
                        .fi-sc.fi-sc-has-gap,
                        .fi-sc-flex,
                        .fi-sc-form,
                        .fi-sc-grid,
                        .fi-grid,
                        .fi-page-content-ctn,
                        .fi-page-content,
                        .fi-page-main,
                        form.fi-form,
                        .fi-form,
                        .fi-page-content > div,
                        .fi-page > div.space-y-6,
                        .ficha-empleado-container,
                        .fi-sc-section-ctn {
                            gap: 0.35rem !important;
                            row-gap: 0.35rem !important;
                        }

                        .fi-section-content .fi-sc.fi-sc-has-gap,
                        .fi-section-content.fi-sc.fi-sc-has-gap {
                            gap: 0.6rem !important;
                            row-gap: 0.4rem !important;
                        }

                        .fi-section,
                        .fi-sc-section {
                            margin-top: 0px !important;
                            margin-bottom: 0.35rem !important;
                        }

                        .fi-section:not(.fi-section-not-contained):not(.fi-aside) > .fi-section-header,
                        .fi-section:not(.fi-section-not-contained).fi-compact:not(.fi-aside) > .fi-section-header,
                        .fi-section > .fi-section-header,
                        .fi-section-header {
                            padding-top: 0.35rem !important;
                            padding-bottom: 0.35rem !important;
                            padding-block: 0.35rem !important;
                            padding-inline: 0.85rem !important;
                            min-height: 0px !important;
                        }

                        .fi-section:not(.fi-section-not-contained):not(.fi-divided) > .fi-section-content-ctn > .fi-section-content,
                        .fi-section:not(.fi-section-not-contained).fi-compact:not(.fi-divided) > .fi-section-content-ctn > .fi-section-content,
                        .fi-section .fi-section-content-ctn > .fi-section-content,
                        .fi-section .fi-section-content,
                        .fi-sc-section .fi-section-content,
                        .fi-section-content {
                            padding-top: 0.35rem !important;
                            padding-bottom: 0.45rem !important;
                            padding-block: 0.4rem !important;
                            padding-inline: 0.85rem !important;
                        }

                        .fi-section:not(.fi-section-not-contained) > .fi-section-content-ctn > .fi-section-footer,
                        .fi-section .fi-section-footer {
                            padding-top: 0.3rem !important;
                            padding-bottom: 0.3rem !important;
                            padding-block: 0.3rem !important;
                            padding-inline: 0.85rem !important;
                        }

                        .fi-section hr,
                        .fi-sc-section hr {
                            margin-top: 0.3rem !important;
                            margin-bottom: 0.3rem !important;
                            margin-block: 0.3rem !important;
                        }

                        .fi-in-entry-wrp,
                        .fi-fo-field-wrp {
                            margin-bottom: 0px !important;
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                        }

                        .fi-in-entry-wrp-label,
                        .fi-fo-field-wrp-label {
                            margin-bottom: 0.1rem !important;
                        }

                        .fi-in-entry-wrp-label dt,
                        .fi-fo-field-wrp-label label,
                        .fi-in-entry-wrp-label span {
                            font-size: 0.725rem !important;
                            line-height: 1.2 !important;
                        }

                        .fi-in-text-item,
                        .fi-in-affixes-ctn {
                            font-size: 0.8rem !important;
                            line-height: 1.25 !important;
                        }

                        /* 6. Tablas y listados compactos en todas las secciones */
                        .fi-resource-users .fi-ta-cell,
                        .fi-resource-users tr.fi-ta-row > td.fi-ta-cell {
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            padding-left: 8px !important;
                            padding-right: 8px !important;
                            height: 34px !important;
                        }
                        .fi-resource-users .fi-ta-cell > div,
                        .fi-resource-users .fi-ta-col-wrp,
                        .fi-resource-users .fi-ta-text,
                        .fi-resource-users .fi-ta-text-item,
                        .fi-resource-users .fi-ta-selection-cell,
                        .fi-resource-users .fi-ta-actions-cell {
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            margin-top: 0px !important;
                            margin-bottom: 0px !important;
                            min-height: 0px !important;
                        }
                        .fi-resource-users tr.fi-ta-row {
                            height: 34px !important;
                        }
                        .fi-resource-users .fi-ta-row td * {
                            font-size: 0.8125rem !important;
                        }
                        .fi-resource-users .fi-badge {
                            padding-top: 1px !important;
                            padding-bottom: 1px !important;
                            padding-left: 6px !important;
                            padding-right: 6px !important;
                            font-size: 0.725rem !important;
                            line-height: 1.2 !important;
                            min-height: 20px !important;
                        }
                        .fi-resource-users .fi-ta-actions button,
                        .fi-resource-users .fi-ta-actions a {
                            padding: 2px !important;
                            min-height: 24px !important;
                            height: 24px !important;
                        }
                        .fi-resource-users .fi-ta-actions svg {
                            width: 15px !important;
                            height: 15px !important;
                        }

                        .fi-ta-table td,
                        .fi-ta-table th,
                        table tbody td,
                        table thead th {
                            padding-top: 0.15rem !important;
                            padding-bottom: 0.15rem !important;
                            padding-left: 0.75rem !important;
                            padding-right: 0.75rem !important;
                        }

                        .fi-ta-table .fi-ta-text,
                        .fi-ta-table .fi-ta-col-wrp,
                        .fi-ta-table .fi-ta-selection-cell,
                        .fi-ta-table .fi-ta-actions-cell {
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            min-height: 0px !important;
                        }

                        .fi-ta-table td .fi-ta-text-item-label,
                        .fi-ta-table td .fi-ta-text-item,
                        table tbody td:first-child,
                        table tbody td.font-semibold,
                        .fi-ta-record td:first-child {
                            font-size: 0.8rem !important; /* ~12.8px */
                            line-height: 1.25 !important;
                            font-weight: 600 !important;
                        }

                        .fi-ta-table tbody tr,
                        table tbody tr {
                            font-size: 0.775rem !important; /* ~12.4px */
                        }

                        .fi-ta-table thead th,
                        table thead th {
                            padding-top: 0.45rem !important;
                            padding-bottom: 0.45rem !important;
                            font-size: 0.675rem !important; /* ~10.8px */
                            letter-spacing: 0.05em !important;
                        }

                        /* 7. Migas de pan profesionales y elegantes (Pills) unificadas para todas las secciones */
                        body.fi-body .fi-breadcrumbs,
                        .fi-breadcrumbs {
                            display: inline-flex !important;
                            align-items: center !important;
                            margin: 0px !important;
                            padding: 0px !important;
                        }

                        body.fi-body .fi-breadcrumbs-list,
                        .fi-breadcrumbs-list {
                            display: inline-flex !important;
                            align-items: center !important;
                            flex-wrap: wrap !important;
                            gap: 0.35rem !important;
                            padding: 0.28rem 0.75rem !important;
                            background: linear-gradient(135deg, rgba(248, 250, 252, 0.95) 0%, rgba(241, 245, 249, 0.9) 100%) !important;
                            border: 1px solid rgba(226, 232, 240, 0.95) !important;
                            border-radius: 9999px !important;
                            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
                            backdrop-filter: blur(8px) !important;
                        }

                        .dark body.fi-body .fi-breadcrumbs-list,
                        .dark .fi-breadcrumbs-list {
                            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.75) 100%) !important;
                            border-color: rgba(255, 255, 255, 0.08) !important;
                            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.25) !important;
                        }

                        body.fi-body .fi-breadcrumbs-item,
                        .fi-breadcrumbs-item {
                            display: inline-flex !important;
                            align-items: center !important;
                            gap: 0.35rem !important;
                            font-size: 0.75rem !important;
                            line-height: 1.25 !important;
                        }

                        body.fi-body .fi-breadcrumbs-item-separator,
                        .fi-breadcrumbs-item-separator {
                            width: 0.85rem !important;
                            height: 0.85rem !important;
                            color: #818cf8 !important;
                            opacity: 0.85 !important;
                        }

                        .dark body.fi-body .fi-breadcrumbs-item-separator,
                        .dark .fi-breadcrumbs-item-separator {
                            color: #6366f1 !important;
                        }

                        body.fi-body .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label,
                        .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label {
                            color: #4f46e5 !important;
                            font-weight: 700 !important;
                            display: inline-flex !important;
                            align-items: center !important;
                        }

                        body.fi-body .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label::before,
                        .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label::before {
                            content: '' !important;
                            display: inline-block !important;
                            width: 6px !important;
                            height: 6px !important;
                            border-radius: 50% !important;
                            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
                            box-shadow: 0 0 6px rgba(99, 102, 241, 0.5) !important;
                            margin-right: 6px !important;
                        }

                        .dark body.fi-body .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label,
                        .dark .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label {
                            color: #a5b4fc !important;
                        }

                        body.fi-body a.fi-breadcrumbs-item-label,
                        a.fi-breadcrumbs-item-label {
                            color: #475569 !important;
                            font-weight: 600 !important;
                            text-decoration: none !important;
                            padding: 0.15rem 0.45rem !important;
                            border-radius: 0.375rem !important;
                            transition: all 0.15s ease-in-out !important;
                        }

                        body.fi-body a.fi-breadcrumbs-item-label:hover,
                        a.fi-breadcrumbs-item-label:hover {
                            color: #4338ca !important;
                            background-color: rgba(99, 102, 241, 0.1) !important;
                        }

                        .dark body.fi-body a.fi-breadcrumbs-item-label,
                        .dark a.fi-breadcrumbs-item-label {
                            color: #94a3b8 !important;
                        }

                        .dark body.fi-body a.fi-breadcrumbs-item-label:hover,
                        .dark a.fi-breadcrumbs-item-label:hover {
                            color: #e0e7ff !important;
                            background-color: rgba(99, 102, 241, 0.25) !important;
                        }

                        body.fi-body .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label,
                        .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label {
                            color: #3730a3 !important;
                            font-weight: 700 !important;
                            background-color: #e0e7ff !important;
                            padding: 0.15rem 0.55rem !important;
                            border-radius: 9999px !important;
                            border: 1px solid rgba(199, 210, 254, 0.8) !important;
                        }

                        .dark body.fi-body .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label,
                        .dark .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label {
                            color: #e0e7ff !important;
                            background-color: rgba(79, 70, 229, 0.25) !important;
                            border-color: rgba(99, 102, 241, 0.4) !important;
                        }

                        body.fi-body .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label,
                        .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label {
                            color: #4338ca !important;
                            background-color: #e0e7ff !important;
                            border: 1px solid rgba(199, 210, 254, 0.8) !important;
                            padding: 0.18rem 0.65rem !important;
                            border-radius: 9999px !important;
                            font-weight: 700 !important;
                        }

                        .dark body.fi-body .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label,
                        .dark .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label {
                            color: #e0e7ff !important;
                            background-color: rgba(79, 70, 229, 0.25) !important;
                            border-color: rgba(99, 102, 241, 0.4) !important;
                        }
                    
.fi-resource-users .fi-ta-cell,
.fi-resource-users tr.fi-ta-row > td,
.fi-resource-users td,
[class*=resource-users] td,
[class*=resource-users] .fi-ta-cell {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-block: 0px !important;
    padding-left: 8px !important;
    padding-right: 8px !important;
    height: 30px !important;
    line-height: 1.2 !important;
}

.fi-resource-users tr.fi-ta-row,
.fi-resource-users tbody tr,
[class*=resource-users] tr {
    height: 30px !important;
}

.fi-resource-users .fi-ta-cell > div,
.fi-resource-users .fi-ta-col-wrp,
.fi-resource-users .fi-ta-text,
.fi-resource-users .fi-ta-text-item,
.fi-resource-users .fi-ta-selection-cell,
.fi-resource-users .fi-ta-actions-cell,
.fi-resource-users .fi-ta-actions,
[class*=resource-users] .fi-ta-cell > div,
[class*=resource-users] .fi-ta-col-wrp,
[class*=resource-users] .fi-ta-text {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-block: 0px !important;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
    margin-block: 0px !important;
    min-height: 0px !important;
}

.fi-resource-users .fi-badge,
[class*=resource-users] .fi-badge {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
    font-size: 0.7rem !important;
    line-height: 1.1 !important;
    min-height: 18px !important;
    height: 18px !important;
}

.fi-resource-users .fi-ta-actions button,
.fi-resource-users .fi-ta-actions a,
.fi-resource-users .fi-icon-btn,
[class*=resource-users] .fi-ta-actions button {
    padding: 1px !important;
    min-height: 22px !important;
    height: 22px !important;
    width: 22px !important;
    min-width: 22px !important;
}

.fi-resource-users .fi-ta-actions svg,
[class*=resource-users] .fi-ta-actions svg {
    width: 14px !important;
    height: 14px !important;
}

/* extra_compact_users */

/* =====================================================================
   USUARIOS - Compactacion maxima de filas y eliminacion de padding
   ===================================================================== */
.fi-resource-users table tbody tr,
.fi-resource-users tr.fi-ta-row,
.fi-resource-users tbody tr,
[class*=resource-users] tbody tr {
    height: 28px !important;
    min-height: 28px !important;
    max-height: 32px !important;
}

.fi-resource-users .fi-ta-cell,
.fi-resource-users tr.fi-ta-row > td,
.fi-resource-users td,
.fi-resource-users th,
[class*=resource-users] td,
[class*=resource-users] th {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-block: 0px !important;
    padding-left: 8px !important;
    padding-right: 8px !important;
    height: 28px !important;
    line-height: 1.2 !important;
}

.fi-resource-users .fi-ta-cell > *,
.fi-resource-users .fi-ta-col,
.fi-resource-users .fi-ta-col-wrp,
.fi-resource-users .fi-ta-text,
.fi-resource-users .fi-ta-text-item,
.fi-resource-users .fi-ta-selection-cell,
.fi-resource-users .fi-ta-actions-cell,
.fi-resource-users .fi-ta-actions,
[class*=resource-users] .fi-ta-cell > *,
[class*=resource-users] .fi-ta-col,
[class*=resource-users] .fi-ta-text {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-block: 0px !important;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
    margin-block: 0px !important;
    min-height: 0px !important;
    height: auto !important;
}

.fi-resource-users .fi-badge,
[class*=resource-users] .fk-badge {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
    font-size: 0.7rem !important;
    line-height: 1.1 !important;
    min-height: 18px !important;
    height: 18px !important;
}

.fi-resource-users .fi-ta-actions button,
.fi-resource-users .fi-ta-actions a,
.fi-resource-users .fi-icon-btn,
[class*=resource-users] .fi-ta-actions button {
    padding: 0px !important;
    min-height: 20px !important;
    height: 20px !important;
    width: 20px !important;
    min-width: 20px !important;
}

.fi-resource-users .fi-ta-actions svg,
[class*=resource-users] .fi-ta-actions svg {
    width: 14px !important;
    height: 14px !important;
}


/* Ocultar barra de busqueda redundante en Usuarios */
.fi-resource-users .fi-ta-header-toolbar,
.fi-resource-users .fi-ta-search-field,
[class*=resource-users] .fi-ta-header-toolbar,
[class*=resource-users] .fi-ta-search-field {
    display: none !important;
}

</style>
                    ";

                    return $css;
                }
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_START,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@include("layouts.navigation")'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                function (): string {
                    $jokes = [
                        "— ¿Cuánto le pongo, jefe? — Échale 10 euros de gasolina y 50 de fe a ver si llego a fin de mes.",
                        "— Buenas, ¿me revisa el aceite y la presión de las ruedas? — Señor, esto es un túnel de lavado, salga del coche por favor.",
                        "— ¿Tiene cambio de 50€? — Sí, claro. — Pues póngame 5€ de diésel y deme los 45€ que tengo que hacer la compra.",
                        "Regla de oro del gasolinero: el cliente que pide 'llénelo' siempre aparca en el lado contrario al del depósito.",
                        "— ¿Gasolina 95 o 98? — La que esté más barata, que el coche tiene sed pero yo tengo hipoteca.",
                        "— Buenas, ¿me pone 2 euros de gasolina? — ¿Qué pasa, que el mechero no le enciende?",
                        "Ese momento mágico en el que intentas clavar el importe exacto en el surtidor y pasa de 19,99€ a 20,01€... Tragedia nacional.",
                        "— Oiga, ¿este túnel de lavado encoge los coches? — No, ¿por qué? — Porque entré con un monovolumen y he salido con un Twingo.",
                        "— ¿Por qué los gasolineros son tan sabios? — Porque manejan los niveles de presión de todo el barrio.",
                        "— Jefe, ¿me limpia el parabrisas? — Pero si viene usted en moto... — Bueno, pues las gafas, no te pongas tiquismiquis.",
                        "— ¿Me mira la presión de las ruedas? — Claro... veo que están bajo mucha presión, igual necesitan terapia.",
                        "— Buenas, ¿el baño está libre? — Sí, pero la llave está atada a una llanta de camión de 40 kilos por seguridad.",
                        "— ¿Qué hace un pistero cuando se aburre? — Contar cuántos conductores intentan estirar la manguera hasta el otro lado del coche.",
                        "— Póngame 20 euros de diésel. — ¿Le cobro con tarjeta o con lágrimas?",
                        "— Oiga, ¿la gasolina sube o baja? — Subir sube siempre, lo que baja es mi paciencia en el turno de noche.",
                        "— ¿Le miro el agua del limpiaparabrisas? — No gracias, si llueve saco la cabeza por la ventanilla.",
                        "— Buenas, ¿acepta tarjeta de puntos? — Sí, pero con los puntos que tiene le llega para un ambientador de pino y una servilleta.",
                        "— ¿Por qué vino en grúa si la gasolinera estaba a 100 metros? — Por confiar en la luz de la reserva hasta el último aliento.",
                        "— ¿Me pone 10€ de 95? — ¿Para llevar o se la bebe aquí?",
                        "El superpoder del empleado de gasolinera: adivinar a la primera cuál es 'el coche gris del fondo'."
                    ];
                    $jokeText = $jokes[array_rand($jokes)];
                    
                    return \Illuminate\Support\Facades\Blade::render('
                        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-center shadow-inner">
                            <p class="text-sm italic font-medium text-amber-800 dark:text-amber-300">
                                "{{ $jokeText }}"
                            </p>
                            <span class="block text-[10px] uppercase font-bold text-amber-500 dark:text-amber-400 tracking-widest mt-2">
                                — Humor de Gasolinera ⛽
                            </span>
                        </div>
                    ', ['jokeText' => $jokeText]);
                }
            )
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsurePasswordIsChanged::class,
                \App\Http\Middleware\EnsureUserIsNotBaja::class,
            ]);
    }
}