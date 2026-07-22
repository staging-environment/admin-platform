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
            
            // Registramos tu recurso de Ofertas de Empleo a mano para saltarnos la limitación de la subcarpeta
            ->resources([
                JobOfferResource::class,
                JobApplicationResource::class,
                EmpleadoResource::class,
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
                    ");
                    if (str_contains(request()->url(), 'recursos-humanos')) {
                        $css .= "
                        <style>
                            .fi-main-ctn,
                            .fi-layout,
                            main,
                            .fi-content {
                                padding-top: 0px !important;
                                margin-top: 0px !important;
                            }
                            .fi-layout {
                                display: flex !important;
                                flex-direction: column !important;
                                grid-template-rows: 1fr !important;
                                gap: 0 !important;
                                min-height: 0 !important;
                            }
                            .fi-main-ctn {
                                display: flex !important;
                                flex-direction: column !important;
                                gap: 0 !important;
                                margin: 0 !important;
                                padding: 0 !important;
                                flex-grow: 1 !important;
                            }
                            .fi-main {
                                padding-top: 0px !important;
                                margin-top: 0px !important;
                            }
                            .fi-header {
                                margin-top: 0px !important;
                                padding-top: 0px !important;
                                margin-bottom: 0.25rem !important;
                                gap: 0.15rem !important;
                            }
                            .fi-breadcrumbs {
                                margin-bottom: 0px !important;
                            }
                        </style>
                        ";
                    }
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
                    $quotes = [
                        "Y verás que la vida es hermosa, si te paras a ver cómo crecen las cosas.",
                        "Vivir a la deriva, sentir que cada día es el primero.",
                        "Dejaré que el viento sople a mi favor, y que me lleve donde quiera, sin buscar explicación.",
                        "Me colé por la rendija de tu alma y me quedé a vivir allí.",
                        "No quiero saber si el cielo es azul o gris, solo quiero saber si estás aquí."
                    ];
                    $quoteText = $quotes[array_rand($quotes)];
                    
                    return \Illuminate\Support\Facades\Blade::render('
                        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-center shadow-inner">
                            <p class="text-sm italic font-medium text-amber-800 dark:text-amber-300">
                                "{{ $quoteText }}"
                            </p>
                            <span class="block text-[10px] uppercase font-bold text-amber-500 dark:text-amber-400 tracking-widest mt-2">
                                — Robe (Roberto Iniesta)
                            </span>
                        </div>
                    ', ['quoteText' => $quoteText]);
                }
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}