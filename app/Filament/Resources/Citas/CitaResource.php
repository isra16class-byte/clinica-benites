<?php

namespace App\Filament\Resources\Citas;

use App\Filament\Resources\Citas\Pages\CreateCita;
use App\Filament\Resources\Citas\Pages\EditCita;
use App\Filament\Resources\Citas\Pages\ListCitas;
use App\Filament\Resources\Citas\Schemas\CitaForm;
use App\Filament\Resources\Citas\Tables\CitasTable;
use App\Models\Cita;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Atención al paciente';

    // Cita no tiene un único campo de texto representativo (es una relación
    // entre paciente/médico/horario), así que se fija un atributo real solo
    // para habilitar la búsqueda global; el título mostrado se personaliza
    // en getGlobalSearchResultTitle().
    protected static ?string $recordTitleAttribute = 'fecha';

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Si el usuario logueado tiene rol medico y está vinculado a un
     * registro de Medico (campo users.medico_id, ver MEMORIA.md sección
     * 10), solo ve sus propias citas. Admin y recepción, y un médico sin
     * vincular todavía, siguen viendo todas (comportamiento anterior) para
     * no bloquear a nadie por una migración de datos incompleta.
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
        return CitaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitasTable::configure($table);
    }

    /**
     * Búsqueda global: por nombre/apellido/cédula del paciente, nombre del
     * médico o el texto de las notas. "Dot notation" busca dentro de las
     * relaciones (paciente., medico.) sin tener que cargar cada resultado.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'paciente.cedula',
            'medico.nombres',
            'medico.apellidos',
            'notas',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Cita — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Médico' => trim("{$record->medico?->nombres} {$record->medico?->apellidos}"),
            'Área' => $record->area?->nombre,
            'Fecha' => Carbon::parse($record->fecha)->format('d/m/Y').' '.Carbon::parse($record->hora_inicio)->format('H:i'),
            'Estado' => ucfirst($record->estado),
        ];
    }

    // Evita N+1: sin esto, cada resultado de la búsqueda global dispararía
    // 3 consultas extra (paciente, médico, área) al armar título y detalles.
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paciente', 'medico', 'area']);
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
            'index' => ListCitas::route('/'),
            'create' => CreateCita::route('/create'),
            'edit' => EditCita::route('/{record}/edit'),
        ];
    }
}
