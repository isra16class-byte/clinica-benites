<?php

namespace App\Filament\Resources\HistoriaClinicas;

use App\Filament\Resources\HistoriaClinicas\Pages\CreateHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Pages\EditHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Pages\ListHistoriaClinicas;
use App\Filament\Resources\HistoriaClinicas\Pages\ViewHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Schemas\HistoriaClinicaForm;
use App\Filament\Resources\HistoriaClinicas\Schemas\HistoriaClinicaInfolist;
use App\Filament\Resources\HistoriaClinicas\Tables\HistoriaClinicasTable;
use App\Models\HistoriaClinica;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HistoriaClinicaResource extends Resource
{
    protected static ?string $model = HistoriaClinica::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // Igual que en Cita: no hay un único campo representativo, se fija uno
    // real solo para habilitar la búsqueda global (el título se personaliza
    // en getGlobalSearchResultTitle()).
    protected static ?string $recordTitleAttribute = 'motivo_consulta';

    public static function canViewAny(): bool
    {
        // Recepción no tiene acceso a historias clínicas
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
        // Eliminar un registro clínico es sensible: solo admin
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Mismo filtro que en CitaResource: un médico vinculado (users.medico_id)
     * solo ve las historias clínicas de sus propios pacientes. Ver
     * MEMORIA.md sección 10.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->isMedico() && $user->medico_id) {
            $query->where('medico_id', $user->medico_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return HistoriaClinicaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HistoriaClinicaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriaClinicasTable::configure($table);
    }

    /**
     * Búsqueda global: por paciente, médico, motivo de consulta o
     * diagnóstico. Respeta el permiso existente (recepción no ve este
     * recurso en el panel, así que tampoco aparece en la búsqueda global,
     * porque Filament ya filtra por canViewAny() antes de buscar).
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'paciente.cedula',
            'medico.nombres',
            'medico.apellidos',
            'motivo_consulta',
            'diagnostico',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Historia clínica — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Médico' => trim("{$record->medico?->nombres} {$record->medico?->apellidos}"),
            'Motivo' => Str::limit($record->motivo_consulta ?? '', 60) ?: null,
            'Diagnóstico' => Str::limit($record->diagnostico ?? '', 60) ?: null,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paciente', 'medico']);
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
            'index' => ListHistoriaClinicas::route('/'),
            'create' => CreateHistoriaClinica::route('/create'),
            'view' => ViewHistoriaClinica::route('/{record}'),
            'edit' => EditHistoriaClinica::route('/{record}/edit'),
        ];
    }
}
