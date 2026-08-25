<?php

namespace App\Filament\Resources\Internamientos;

use App\Filament\Resources\Internamientos\Pages\CreateInternamiento;
use App\Filament\Resources\Internamientos\Pages\EditInternamiento;
use App\Filament\Resources\Internamientos\Pages\ListInternamientos;
use App\Filament\Resources\Internamientos\Schemas\InternamientoForm;
use App\Filament\Resources\Internamientos\Tables\InternamientosTable;
use App\Models\Internamiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Internamientos — Hospitalización/UCI/UCIN (sección 6.2 de MEMORIA.md,
 * grupo 1). Es operación clínica del día a día, así que sigue el mismo
 * criterio de permisos que Citas: admin y recepción agendan/administran el
 * ingreso, médico ve y edita (registra evolución/alta) sin poder borrar.
 */
class InternamientoResource extends Resource
{
    protected static ?string $model = Internamiento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationGroup = 'Infraestructura';

    // Igual que Cita: no hay un único campo representativo, se fija uno
    // real solo para habilitar la búsqueda global.
    protected static ?string $recordTitleAttribute = 'motivo';

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
     * Mismo filtro que CitaResource: un médico vinculado (users.medico_id)
     * solo ve los internamientos donde es el responsable.
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
        return InternamientoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InternamientosTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'paciente.cedula',
            'medico.nombres',
            'medico.apellidos',
            'motivo',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Internamiento — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cama' => $record->cama?->numero,
            'Médico responsable' => trim("{$record->medico?->nombres} {$record->medico?->apellidos}"),
            'Ingreso' => Carbon::parse($record->fecha_ingreso)->format('d/m/Y H:i'),
            'Estado' => $record->activo() ? 'Internado' : 'Dado de alta',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paciente', 'medico', 'cama']);
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
            'index' => ListInternamientos::route('/'),
            'create' => CreateInternamiento::route('/create'),
            'edit' => EditInternamiento::route('/{record}/edit'),
        ];
    }
}
