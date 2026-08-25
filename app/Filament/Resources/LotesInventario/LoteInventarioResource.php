<?php

namespace App\Filament\Resources\LotesInventario;

use App\Filament\Resources\LotesInventario\Pages\CreateLoteInventario;
use App\Filament\Resources\LotesInventario\Pages\EditLoteInventario;
use App\Filament\Resources\LotesInventario\Pages\ListLotesInventario;
use App\Filament\Resources\LotesInventario\Schemas\LoteInventarioForm;
use App\Filament\Resources\LotesInventario\Tables\LotesInventarioTable;
use App\Models\LoteInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Lotes de cada ítem del catálogo (trazabilidad FEFO, sección 6.3 de
 * MEMORIA.md). Mismos permisos que el catálogo (ItemInventarioResource):
 * admin gestiona todo, recepción solo consulta, médico sin acceso.
 */
class LoteInventarioResource extends Resource
{
    protected static ?string $model = LoteInventario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $recordTitleAttribute = 'numero_lote';

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
        return LoteInventarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LotesInventarioTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_lote', 'item.nombre'];
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
            'index' => ListLotesInventario::route('/'),
            'create' => CreateLoteInventario::route('/create'),
            'edit' => EditLoteInventario::route('/{record}/edit'),
        ];
    }
}
