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

                    // Estilos globales de tipografía compacta y márgenes reducidos para todas las secciones
                    $css .= "
                    <style>
                        /* Ocultar el título principal de página en todas las secciones */
                        .fi-header-heading,
                        h1.fi-header-heading,
                        .fi-page-header .fi-header-heading,
                        .fi-header h1 {
                            display: none !important;
                        }

                        /* Margen equilibrado en la cabecera de página */
                        .fi-header {
                            margin-top: 0.85rem !important;
                            margin-bottom: 0.65rem !important;
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                            gap: 0.25rem !important;
                        }
                        .fi-page-header-main-ctn {
                            padding-top: 0.15rem !important;
                            padding-bottom: 0.15rem !important;
                        }

                        /* Títulos secundarios reducidos en todas las secciones */
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

                        /* Reducción de márgenes entre bloques y secciones */
                        .fi-page-content,
                        .fi-page-content > div,
                        .fi-page > div.space-y-6 {
                            gap: 0.875rem !important;
                        }
                        .fi-section {
                            margin-bottom: 0.875rem !important;
                        }

                        /* 5. Tablas y listados compactos en todas las secciones */
                        .fi-ta-table td,
                        .fi-ta-table th,
                        table tbody td,
                        table thead th {
                            padding-top: 0.5rem !important; /* 8px en vez de 16px */
                            padding-bottom: 0.5rem !important;
                            padding-left: 0.85rem !important;
                            padding-right: 0.85rem !important;
                        }

                        /* Títulos y textos principales en filas de listados (ej. nombre de empleado) */
                        .fi-ta-table td .fi-ta-text-item-label,
                        .fi-ta-table td .fi-ta-text-item,
                        table tbody td:first-child,
                        table tbody td.font-semibold,
                        .fi-ta-record td:first-child {
                            font-size: 0.8rem !important; /* ~12.8px */
                            line-height: 1.25 !important;
                            font-weight: 600 !important;
                        }

                        /* Tipografía general más compacta en el contenido de las tablas */
                        .fi-ta-table tbody tr,
                        table tbody tr {
                            font-size: 0.775rem !important; /* ~12.4px */
                        }

                        /* Encabezados de tablas reducidos */
                        .fi-ta-table thead th,
                        table thead th {
                            padding-top: 0.45rem !important;
                            padding-bottom: 0.45rem !important;
                            font-size: 0.675rem !important; /* ~10.8px */
                            letter-spacing: 0.05em !important;
                        }
                    </style>
                    ";

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
                                margin-top: 0.85rem !important;
                                padding-top: 0px !important;
                                margin-bottom: 0.65rem !important;
                                gap: 0.15rem !important;
                            }
                            .fi-page-dashboard .fi-header,
                            .fi-dashboard-page .fi-header {
                                display: block !important;
                                margin-top: 0.85rem !important;
                                margin-bottom: 0.65rem !important;
                            }
                            .fi-breadcrumbs {
                                display: inline-flex !important;
                                align-items: center !important;
                                margin-top: 0px !important;
                                margin-bottom: 0px !important;
                            }
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
                            .dark .fi-breadcrumbs-list {
                                background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.75) 100%) !important;
                                border-color: rgba(255, 255, 255, 0.08) !important;
                                box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.25) !important;
                            }
                            .fi-breadcrumbs-item {
                                display: inline-flex !important;
                                align-items: center !important;
                                gap: 0.35rem !important;
                                font-size: 0.75rem !important;
                                line-height: 1.25 !important;
                            }
                            .fi-breadcrumbs-item-separator {
                                width: 0.85rem !important;
                                height: 0.85rem !important;
                                color: #818cf8 !important;
                                opacity: 0.85 !important;
                            }
                            .dark .fi-breadcrumbs-item-separator {
                                color: #6366f1 !important;
                            }
                            .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label {
                                color: #4f46e5 !important;
                                font-weight: 700 !important;
                                display: inline-flex !important;
                                align-items: center !important;
                            }
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
                            .dark .fi-breadcrumbs-item:first-child .fi-breadcrumbs-item-label {
                                color: #a5b4fc !important;
                            }
                            a.fi-breadcrumbs-item-label {
                                color: #475569 !important;
                                font-weight: 600 !important;
                                text-decoration: none !important;
                                padding: 0.15rem 0.45rem !important;
                                border-radius: 0.375rem !important;
                                transition: all 0.15s ease-in-out !important;
                            }
                            a.fi-breadcrumbs-item-label:hover {
                                color: #4338ca !important;
                                background-color: rgba(99, 102, 241, 0.1) !important;
                            }
                            .dark a.fi-breadcrumbs-item-label {
                                color: #94a3b8 !important;
                            }
                            .dark a.fi-breadcrumbs-item-label:hover {
                                color: #e0e7ff !important;
                                background-color: rgba(99, 102, 241, 0.25) !important;
                            }
                            .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label {
                                color: #3730a3 !important;
                                font-weight: 700 !important;
                                background-color: #e0e7ff !important;
                                padding: 0.15rem 0.55rem !important;
                                border-radius: 9999px !important;
                                border: 1px solid rgba(199, 210, 254, 0.8) !important;
                            }
                            .dark .fi-breadcrumbs-item:last-child .fi-breadcrumbs-item-label {
                                color: #e0e7ff !important;
                                background-color: rgba(79, 70, 229, 0.25) !important;
                                border-color: rgba(99, 102, 241, 0.4) !important;
                            }
                            .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label {
                                color: #4338ca !important;
                                background-color: #e0e7ff !important;
                                border: 1px solid rgba(199, 210, 254, 0.8) !important;
                                padding: 0.18rem 0.65rem !important;
                                border-radius: 9999px !important;
                                font-weight: 700 !important;
                            }
                            .dark .fi-breadcrumbs-item:first-child:last-child .fi-breadcrumbs-item-label {
                                color: #e0e7ff !important;
                                background-color: rgba(79, 70, 229, 0.25) !important;
                                border-color: rgba(99, 102, 241, 0.4) !important;
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