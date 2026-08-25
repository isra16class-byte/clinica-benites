<?php

namespace App\Filament\Resources\ItemsInventario;

use App\Filament\Resources\ItemsInventario\Pages\CreateItemInventario;
use App\Filament\Resources\ItemsInventario\Pages\EditItemInventario;
use App\Filament\Resources\ItemsInventario\Pages\ListItemsInventario;
use App\Filament\Resources\ItemsInventario\Schemas\ItemInventarioForm;
use App\Filament\Resources\ItemsInventario\Tables\ItemsInventarioTable;
use App\Models\ItemInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Catálogo de medicamentos e insumos (sección 6.3 de MEMORIA.md).
 *
 * Permisos: igual criterio que Áreas/Médicos (catálogo/configuración, no
 * operación diaria) — admin gestiona todo, recepción solo consulta, médico
 * sin acceso. No existe todavía un rol "farmacia" separado (el contacto
 * interno mencionó que el módulo vive en farmacia/quirófano/admisión/
 * facturación, pero el sistema hoy solo tiene admin/recepcion/medico) — se
 * asume que quien gestiona el catálogo cae dentro de "recepción" u "admin",
 * ajustable si la clínica confirma un rol propio para farmacia.
 */
class ItemInventarioResource extends Resource
{
    protected static ?string $model = ItemInventario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ItemInventarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemsInventarioTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'tipo'];
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
            'index' => ListItemsInventario::route('/'),
            'create' => CreateItemInventario::route('/create'),
            'edit' => EditItemInventario::route('/{record}/edit'),
        ];
    }
}
