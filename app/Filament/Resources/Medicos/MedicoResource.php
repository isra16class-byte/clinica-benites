<?php

namespace App\Filament\Resources\Medicos;

use App\Filament\Resources\Medicos\Pages\CreateMedico;
use App\Filament\Resources\Medicos\Pages\EditMedico;
use App\Filament\Resources\Medicos\Pages\ListMedicos;
use App\Filament\Resources\Medicos\Schemas\MedicoForm;
use App\Filament\Resources\Medicos\Tables\MedicosTable;
use App\Models\Medico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicoResource extends Resource
{
    protected static ?string $model = Medico::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombres';

    public static function canViewAny(): bool
    {
        return true;
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
        return MedicoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicosTable::configure($table);
    }

    /**
     * Búsqueda global: por nombre, apellido, email o teléfono, no solo por
     * el nombre de pila (mejora sobre el $recordTitleAttribute por defecto).
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombres', 'apellidos', 'email', 'telefono'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return trim("{$record->nombres} {$record->apellidos}");
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Área' => $record->area?->nombre,
            'Teléfono' => $record->telefono,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('area');
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
            'index' => ListMedicos::route('/'),
            'create' => CreateMedico::route('/create'),
            'edit' => EditMedico::route('/{record}/edit'),
        ];
    }
}
