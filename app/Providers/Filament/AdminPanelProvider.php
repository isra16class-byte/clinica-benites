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
                        /* Acento turquesa extra: línea bajo la barra superior,
                           fondo del sidebar levemente teñido, e ítem de menú
                           activo con más presencia de color. */
                        .fi-topbar > nav {
                            border-bottom-width: 2px;
                            border-bottom-color: var(--primary-400);
                        }
                        .fi-sidebar {
                            background-color: color-mix(in srgb, var(--primary-500) 4%, white);
                        }
                        .fi-sidebar-item.fi-active .fi-sidebar-item-button {
                            background-color: color-mix(in srgb, var(--primary-500) 14%, white) !important;
                        }

                        /* Tablas: encabezado con tinte turquesa y línea de
                           acento debajo, para que se note más que hoy (que
                           queda todo blanco/gris). */
                        .fi-ta-header-cell {
                            background-color: color-mix(in srgb, var(--primary-500) 6%, white);
                        }
                        .fi-ta-table thead tr {
                            border-bottom: 2px solid var(--primary-300);
                        }

                        /* Botones de acción en tabla (ej. "Editar"), para que
                           tengan más presencia de color y no se pierdan en
                           gris junto al resto de la fila. */
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
