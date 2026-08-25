<?php

namespace App\Filament\Resources\OrdenesEstudio;

use App\Filament\Resources\OrdenesEstudio\Pages\CreateOrdenEstudio;
use App\Filament\Resources\OrdenesEstudio\Pages\EditOrdenEstudio;
use App\Filament\Resources\OrdenesEstudio\Pages\ListOrdenesEstudio;
use App\Filament\Resources\OrdenesEstudio\Schemas\OrdenEstudioForm;
use App\Filament\Resources\OrdenesEstudio\Tables\OrdenesEstudioTable;
use App\Models\OrdenEstudio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Órdenes de estudio — Laboratorio, Rayos X, Ecografía, Centro de Imagen,
 * Endoscopía, Gastroenterología, Procedimientos Ambulatorios (sección 6.2
 * de MEMORIA.md, grupo 3). A diferencia de Citas/Internamientos/Cirugías,
 * acá el médico también puede crear (solicitar la orden), no solo editar
 * — es quien la origina.
 */
class OrdenEstudioResource extends Resource
{
    protected static ?string $model = OrdenEstudio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Infraestructura';

    protected static ?string $recordTitleAttribute = 'tipo';

    public static function canViewAny(): bool
    {
        return Auth::user() !== null;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion() || $user?->isMedico();
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
     * Un médico vinculado (users.medico_id) solo ve las órdenes que él
     * mismo solicitó.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->isMedico() && $user->medico_id) {
            $query->where('medico_solicitante_id', $user->medico_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OrdenEstudioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdenesEstudioTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'tipo',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Orden de estudio — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tipo' => ucfirst(str_replace('_', ' ', $record->tipo)),
            'Solicitante' => trim("{$record->medicoSolicitante?->nombres} {$record->medicoSolicitante?->apellidos}"),
            'Estado' => ucfirst(str_replace('_', ' ', $record->estado)),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['paciente', 'medicoSolicitante']);
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
            'index' => ListOrdenesEstudio::route('/'),
            'create' => CreateOrdenEstudio::route('/create'),
            'edit' => EditOrdenEstudio::route('/{record}/edit'),
        ];
    }
}
