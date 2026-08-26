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
            ->brandLogo(asset('images/logo-horizontal.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
            ->colors([
                'primary' => Color::hex('#0C447C'),
            ])
            ->navigationGroups([
                'Atención al paciente',
                'Facturación',
                'Infraestructura',
                'Inventario',
                'Administración',
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->userMenuItems([
                'profile' => fn (\Filament\Actions\Action $action) => $action
                    ->label(fn () => new \Illuminate\Support\HtmlString(
                        '<span class="fi-user-menu-header-name">' .
                            e(filament()->getUserName(filament()->auth()->user())) .
                        '</span>' .
                        '<span class="fi-user-menu-header-role fi-user-menu-header-role-' .
                            e(filament()->auth()->user()->rol ?? 'default') . '">' .
                            e(match (filament()->auth()->user()->rol ?? null) {
                                'admin' => 'Administrador',
                                'recepcion' => 'Recepción',
                                'medico' => 'Médico',
                                default => 'Sin rol asignado',
                            }) .
                        '</span>'
                    ))
                    ->url(null)
                    ->icon(null),
                'edit_profile' => fn () => \Filament\Actions\Action::make('edit_profile')
                    ->label('Editar perfil')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedPencilSquare)
                    ->url(fn (): ?string => filament()->getProfileUrl())
                    ->sort(-1),
            ])
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
