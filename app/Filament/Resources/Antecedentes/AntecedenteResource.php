<?php

namespace App\Filament\Resources\Antecedentes;

use App\Filament\Resources\Antecedentes\Pages\CreateAntecedente;
use App\Filament\Resources\Antecedentes\Pages\EditAntecedente;
use App\Filament\Resources\Antecedentes\Pages\ListAntecedentes;
use App\Filament\Resources\Antecedentes\Schemas\AntecedenteForm;
use App\Filament\Resources\Antecedentes\Tables\AntecedentesTable;
use App\Models\Antecedente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AntecedenteResource extends Resource
{
    protected static ?string $model = Antecedente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Atención al paciente';

    protected static ?string $recordTitleAttribute = 'descripcion';

    /**
     * Mismo criterio que AlergiaResource/HistoriaClinicaResource: dato
     * clínico sensible, recepción no tiene acceso. Igual que Alergia,
     * tampoco es una fila confirmada por el cliente en MEMORIA.md sección
     * 10 todavía — se aplica por analogía con el resto del expediente
     * clínico.
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isMedico();
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
        // Igual que Alergia/Historia Clínica: eliminar un dato clínico
        // sensible queda reservado a admin.
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Mismo filtro que AlergiaResource: un médico vinculado
     * (users.medico_id) solo ve antecedentes de pacientes con los que tiene
     * una historia clínica registrada. Ver MEMORIA.md sección 10.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->isMedico() && $user->medico_id) {
            $query->whereHas('paciente.historiaClinicas', function (Builder $q) use ($user) {
                $q->where('medico_id', $user->medico_id);
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return AntecedenteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AntecedentesTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['descripcion', 'paciente.nombres', 'paciente.apellidos', 'paciente.cedula'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Antecedente ({$record->categoria}) — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Categoría' => ucfirst($record->categoria),
            'Descripción' => str($record->descripcion)->limit(60),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('paciente');
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
            'index' => ListAntecedentes::route('/'),
            'create' => CreateAntecedente::route('/create'),
            'edit' => EditAntecedente::route('/{record}/edit'),
        ];
    }
}
