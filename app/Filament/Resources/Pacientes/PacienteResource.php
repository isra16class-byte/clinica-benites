<?php

namespace App\Filament\Resources\Pacientes;

use App\Filament\Resources\Pacientes\Pages\CreatePaciente;
use App\Filament\Resources\Pacientes\Pages\EditPaciente;
use App\Filament\Resources\Pacientes\Pages\ListPacientes;
use App\Filament\Resources\Pacientes\Schemas\PacienteForm;
use App\Filament\Resources\Pacientes\Tables\PacientesTable;
use App\Models\Paciente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombres';

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
        // Admin, recepción y médico pueden editar (médico no puede eliminar)
        return true;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PacienteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PacientesTable::configure($table);
    }

    /**
     * Búsqueda global: por nombre, apellido, cédula o teléfono. Antes solo
     * buscaba por $recordTitleAttribute ('nombres'), lo que no permitía
     * encontrar un paciente por apellido o cédula desde el buscador.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombres', 'apellidos', 'cedula', 'telefono'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return trim("{$record->nombres} {$record->apellidos}");
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cédula' => $record->cedula,
            'Teléfono' => $record->telefono,
        ];
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
            'index' => ListPacientes::route('/'),
            'create' => CreatePaciente::route('/create'),
            'edit' => EditPaciente::route('/{record}/edit'),
        ];
    }
}
