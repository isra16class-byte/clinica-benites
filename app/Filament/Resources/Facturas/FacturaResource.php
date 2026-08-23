<?php

namespace App\Filament\Resources\Facturas;

use App\Filament\Resources\Facturas\Pages\CreateFactura;
use App\Filament\Resources\Facturas\Pages\EditFactura;
use App\Filament\Resources\Facturas\Pages\ListFacturas;
use App\Filament\Resources\Facturas\Schemas\FacturaForm;
use App\Filament\Resources\Facturas\Tables\FacturasTable;
use App\Models\Factura;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // Igual que en Cita e HistoriaClinica: solo para habilitar la búsqueda
    // global, el título real se arma en getGlobalSearchResultTitle().
    protected static ?string $recordTitleAttribute = 'estado_pago';

    public static function canViewAny(): bool
    {
        // Médico no tiene acceso a facturación
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isRecepcion();
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
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return FacturaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacturasTable::configure($table);
    }

    /**
     * Búsqueda global: por paciente, estado de pago o método de pago.
     * Médico no ve este recurso en el panel, y tampoco en la búsqueda
     * global (Filament ya filtra por canViewAny() antes de buscar).
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'paciente.nombres',
            'paciente.apellidos',
            'paciente.cedula',
            'estado_pago',
            'metodo_pago',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $paciente = trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}");

        return "Factura — {$paciente}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Monto' => '$'.number_format((float) $record->monto, 2),
            'Estado de pago' => ucfirst($record->estado_pago),
            'Fecha' => Carbon::parse($record->fecha)->format('d/m/Y'),
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
            'index' => ListFacturas::route('/'),
            'create' => CreateFactura::route('/create'),
            'edit' => EditFactura::route('/{record}/edit'),
        ];
    }
}
