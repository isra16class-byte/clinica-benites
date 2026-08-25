<?php

namespace App\Filament\Resources\Quirofanos;

use App\Filament\Resources\Quirofanos\Pages\CreateQuirofano;
use App\Filament\Resources\Quirofanos\Pages\EditQuirofano;
use App\Filament\Resources\Quirofanos\Pages\ListQuirofanos;
use App\Filament\Resources\Quirofanos\Schemas\QuirofanoForm;
use App\Filament\Resources\Quirofanos\Tables\QuirofanosTable;
use App\Models\Quirofano;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Quirófanos — Central de Quirófanos (sección 6.2 de MEMORIA.md, grupo 2).
 * A diferencia de `Cama`, tiene un campo `estado` editable con más pasos
 * que libre/ocupado (preparación → en cirugía → limpieza → libre), ajuste
 * validado con investigación externa — ver CamaResource para el criterio
 * de permisos, es el mismo (catálogo de recurso físico).
 */
class QuirofanoResource extends Resource
{
    protected static ?string $model = Quirofano::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationGroup = 'Infraestructura';

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
        return QuirofanoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuirofanosTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero', 'nombre'];
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
            'index' => ListQuirofanos::route('/'),
            'create' => CreateQuirofano::route('/create'),
            'edit' => EditQuirofano::route('/{record}/edit'),
        ];
    }
}
