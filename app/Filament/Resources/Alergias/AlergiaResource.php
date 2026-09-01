<?php

namespace App\Filament\Resources\Alergias;

use App\Filament\Resources\Alergias\Pages\CreateAlergia;
use App\Filament\Resources\Alergias\Pages\EditAlergia;
use App\Filament\Resources\Alergias\Pages\ListAlergias;
use App\Filament\Resources\Alergias\Schemas\AlergiaForm;
use App\Filament\Resources\Alergias\Tables\AlergiasTable;
use App\Models\Alergia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AlergiaResource extends Resource
{
    protected static ?string $model = Alergia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Atención al paciente';

    protected static ?string $recordTitleAttribute = 'alergeno';

    /**
     * Mismo criterio que HistoriaClinicaResource: dato clínico sensible,
     * recepción no tiene acceso. Nota: la matriz de permisos de MEMORIA.md
     * sección 10 no cubre todavía este módulo (queda pendiente confirmarlo
     * con el cliente) — se aplica por ahora el mismo criterio que el resto
     * del expediente clínico, no una decisión ya validada.
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
        // Igual que Historia Clínica: eliminar un dato clínico sensible
        // queda reservado a admin.
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Mismo filtro que HistoriaClinicaResource/CitaResource: un médico
     * vinculado (users.medico_id) solo ve alergias de pacientes con los que
     * tiene una historia clínica registrada. Ver MEMORIA.md sección 10.
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
        return AlergiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlergiasTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['alergeno', 'paciente.nombres', 'paciente.apellidos', 'paciente.cedula'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Alergia a {$record->alergeno} — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tipo' => ucfirst($record->tipo),
            'Severidad' => ucfirst($record->severidad),
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
            'index' => ListAlergias::route('/'),
            'create' => CreateAlergia::route('/create'),
            'edit' => EditAlergia::route('/{record}/edit'),
        ];
    }
}
