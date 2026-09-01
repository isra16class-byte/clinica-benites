<?php

namespace App\Filament\Resources\Facturas\RelationManagers;

use App\Models\LineaFactura;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Detalle de la factura (MEMORIA.md sección 6): el SRI exige líneas, no un
 * monto suelto. `subtotal` se calcula solo (LineaFactura::booted()), no es
 * un campo del formulario — evita que quede desincronizado de
 * cantidad×precio-descuento. Cada guardado/borrado dispara
 * Factura::recalcularTotales(), así que el header de la factura (tab
 * anterior) siempre refleja la suma real de las líneas sin acción manual.
 *
 * Mismo caveat que Alergias/Antecedentes: sin `vendor/` en este sandbox, no
 * se pudo correr esto contra el entorno real — revisado a mano contra el
 * patrón ya usado en el proyecto (RelationManager de Alergias).
 */
class LineasFacturaRelationManager extends RelationManager
{
    protected static string $relationship = 'lineas';

    protected static ?string $title = 'Líneas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descripcion')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('cantidad')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('precio_unitario')
                    ->label('Precio unitario')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('descuento')
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
                Select::make('codigo_iva')
                    ->label('Tarifa de IVA')
                    ->options(array_map(fn (array $tarifa): string => $tarifa['label'], LineaFactura::TARIFAS_IVA))
                    ->native(false)
                    ->required()
                    ->default('0')
                    ->helperText('La mayoría de servicios de salud llevan 0% (LRTI art. 55-56). No confirmado con un contador — cambiar si corresponde.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                TextColumn::make('descripcion'),
                TextColumn::make('cantidad'),
                TextColumn::make('precio_unitario')
                    ->label('Precio unitario')
                    ->money('USD'),
                TextColumn::make('descuento')
                    ->money('USD'),
                TextColumn::make('codigo_iva')
                    ->label('IVA')
                    ->formatStateUsing(fn (string $state): string => LineaFactura::TARIFAS_IVA[$state]['label'] ?? $state),
                TextColumn::make('subtotal')
                    ->money('USD'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
