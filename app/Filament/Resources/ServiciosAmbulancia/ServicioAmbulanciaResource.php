<?php

namespace App\Filament\Resources\ServiciosAmbulancia;

use App\Filament\Resources\ServiciosAmbulancia\Pages\CreateServicioAmbulancia;
use App\Filament\Resources\ServiciosAmbulancia\Pages\EditServicioAmbulancia;
use App\Filament\Resources\ServiciosAmbulancia\Pages\ListServiciosAmbulancia;
use App\Filament\Resources\ServiciosAmbulancia\Schemas\ServicioAmbulanciaForm;
use App\Filament\Resources\ServiciosAmbulancia\Tables\ServiciosAmbulanciaTable;
use App\Models\ServicioAmbulancia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Servicios de ambulancia (sección 6.2 de MEMORIA.md, grupo 5) — el grupo
 * de menor prioridad y conexión más débil con el resto del dominio, por
 * eso el permiso de médico se limita a solo ver, no participa
 * directamente del transporte.
 */
class ServicioAmbulanciaResource extends Resource
{
    protected static ?string $model = ServicioAmbulancia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Infraestructura';

    protected static ?string $recordTitleAttribute = 'destino';

    public static function canViewAny(): bool
    {
        return Auth::user() !== null;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ServicioAmbulanciaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiciosAmbulanciaTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['origen', 'destino', 'motivo'];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiciosAmbulancia::route('/'),
            'create' => CreateServicioAmbulancia::route('/create'),
            'edit' => EditServicioAmbulancia::route('/{record}/edit'),
        ];
    }
}
