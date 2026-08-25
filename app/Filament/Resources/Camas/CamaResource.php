<?php

namespace App\Filament\Resources\Camas;

use App\Filament\Resources\Camas\Pages\CreateCama;
use App\Filament\Resources\Camas\Pages\EditCama;
use App\Filament\Resources\Camas\Pages\ListCamas;
use App\Filament\Resources\Camas\Schemas\CamaForm;
use App\Filament\Resources\Camas\Tables\CamasTable;
use App\Models\Cama;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Camas de Hospitalización, UCI y UCIN (sección 6.2 de MEMORIA.md, grupo 1).
 *
 * Permisos: catálogo/configuración de un recurso físico, mismo criterio que
 * Áreas/Médicos — admin gestiona todo, recepción solo consulta (necesita ver
 * disponibilidad para admitir), médico solo consulta. No hay "eliminar"
 * accesible a nadie salvo admin, protegido además contra borrar una cama con
 * internamientos.
 */
class CamaResource extends Resource
{
    protected static ?string $model = Cama::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Infraestructura';

    protected static ?string $recordTitleAttribute = 'numero';

    public static function canViewAny(): bool
    {
        return Auth::user() !== null;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
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
        return CamaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CamasTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero', 'tipo'];
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
            'index' => ListCamas::route('/'),
            'create' => CreateCama::route('/create'),
            'edit' => EditCama::route('/{record}/edit'),
        ];
    }
}
