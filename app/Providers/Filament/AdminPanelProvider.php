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