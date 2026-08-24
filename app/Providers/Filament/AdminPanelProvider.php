<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
            ->brandName('Clínica Benites')
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/icon.svg'))
            ->colors([
                'primary' => Color::Cyan,
                'gray' => Color::Slate,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                        /* Regla 60/30/10 (neutro/estructural/acento) y separar
                           el color de marca del color semántico de estado
                           (los badges de "confirmada"/"cancelada"/"pendiente"
                           ya usan color con significado propio — no hay que
                           competir con eso pintando todo turquesa). Patrón:
                           sidebar oscuro con identidad propia (común en
                           paneles SaaS), contenido y tablas en blanco/gris
                           para que los datos y los badges se lean bien, y
                           turquesa reservado a lo accionable (botones,
                           enlaces, ítem de menú activo). */

                        /* Sidebar: fondo turquesa oscuro sólido en vez de un
                           tinte casi imperceptible sobre blanco. */
                        .fi-sidebar {
                            background-color: var(--primary-900);
                        }
                        .fi-sidebar .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-group-label,
                        .fi-sidebar .fi-icon {
                            color: color-mix(in srgb, white 70%, var(--primary-900));
                        }
                        .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-item-button:hover .fi-icon {
                            color: #ffffff;
                        }
                        .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button {
                            background-color: var(--primary-700) !important;
                        }
                        .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-item.fi-active .fi-icon {
                            color: #ffffff !important;
                        }

                        /* Barra superior: línea de acento fina, nada más. */
                        .fi-topbar > nav {
                            border-bottom-width: 2px;
                            border-bottom-color: var(--primary-400);
                        }

                        /* Tablas: encabezado neutro (blanco/gris) con una
                           línea de acento debajo — se descartó teñir todo el
                           encabezado de color (versión anterior) porque
                           competía visualmente con los badges de estado. */
                        .fi-ta-table thead tr {
                            border-bottom: 2px solid var(--primary-400);
                        }

                        /* Botones de acción en tabla (ej. "Editar") en color
                           primario, reservado a lo accionable. */
                        .fi-ta-actions .fi-btn,
                        .fi-ta-actions .fi-link {
                            color: var(--primary-600) !important;
                        }
                    </style>
                    HTML,
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
