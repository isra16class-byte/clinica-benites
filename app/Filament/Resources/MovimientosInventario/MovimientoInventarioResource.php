<?php

namespace App\Filament\Resources\MovimientosInventario;

use App\Filament\Resources\MovimientosInventario\Pages\CreateMovimientoInventario;
use App\Filament\Resources\MovimientosInventario\Pages\EditMovimientoInventario;
use App\Filament\Resources\MovimientosInventario\Pages\ListMovimientosInventario;
use App\Filament\Resources\MovimientosInventario\Schemas\MovimientoInventarioForm;
use App\Filament\Resources\MovimientosInventario\Tables\MovimientosInventarioTable;
use App\Models\MovimientoInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Movimientos de inventario — entradas, salidas, traslados y ajustes
 * (sección 6.3 de MEMORIA.md). Es el registro operativo del día a día, así
 * que sigue el mismo criterio de permisos que Citas/Facturas: admin y
 * recepción gestionan todo, médico sin acceso (no existe todavía un rol
 * propio de "farmacia"/"quirófano" — ver nota en ItemInventarioResource).
 */
class MovimientoInventarioResource extends Resource
{
    protected static ?string $model = MovimientoInventario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $recordTitleAttribute = 'tipo_movimiento';

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return MovimientoInventarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MovimientosInventarioTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['lote.numero_lote', 'tipo_movimiento'];
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
            'index' => ListMovimientosInventario::route('/'),
            'create' => CreateMovimientoInventario::route('/create'),
            'edit' => EditMovimientoInventario::route('/{record}/edit'),
        ];
    }
}
