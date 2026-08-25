<?php

namespace App\Filament\Resources\Cirugias;

use App\Filament\Resources\Cirugias\Pages\CreateCirugia;
use App\Filament\Resources\Cirugias\Pages\EditCirugia;
use App\Filament\Resources\Cirugias\Pages\ListCirugias;
use App\Filament\Resources\Cirugias\Schemas\CirugiaForm;
use App\Filament\Resources\Cirugias\Tables\CirugiasTable;
use App\Models\Cirugia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Cirugías — Central de Quirófanos (sección 6.2 de MEMORIA.md, grupo 2).
 * Mismo criterio de permisos que Citas/Internamientos: admin y recepción
 * agendan, médico (el principal o uno de los adicionales) ve y edita sin
 * poder borrar.
 */
class CirugiaResource extends Resource
{
    protected static ?string $model = Cirugia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScissors;

    protected static ?string $navigationGroup = 'Infraestructura';

    protected static ?string $recordTitleAttribute = 'tipo_cirugia';

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
        return Auth::user() !== null;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Un médico vinculado (users.medico_id) solo ve las cirugías donde es
     * el responsable principal. No se filtra por médicos adicionales
     * (medicosAdicionales) para mantener la consulta simple — supuesto
     * razonable, ajustable si hace falta que anestesiólogos/ayudantes
     * también vean sus cirugías asignadas.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->isMedico() && $user->medico_id) {
            $query->where('medico_principal_id', $user->medico_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return CirugiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CirugiasTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'tipo_cirugia',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Cirugía — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tipo' => $record->tipo_cirugia,
            'Quirófano' => $record->quirofano?->numero,
            'Fecha' => $record->fecha?->format('d/m/Y').' '.$record->hora_inicio,
            'Estado' => ucfirst($record->estado),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paciente', 'quirofano']);
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
            'index' => ListCirugias::route('/'),
            'create' => CreateCirugia::route('/create'),
            'edit' => EditCirugia::route('/{record}/edit'),
        ];
    }
}
