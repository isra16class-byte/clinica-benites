<?php

namespace App\Filament\Resources\ItemsInventario\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItemInventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('tipo')
                    ->options([
                        'medicamento' => 'Medicamento',
                        'insumo' => 'Insumo',
                    ])
                    ->required(),
                TextInput::make('unidad_medida')
                    ->label('Unidad de medida')
                    ->helperText('Ej. unidad, caja, ml, mg, frasco')
                    ->required(),
                TextInput::make('stock_minimo')
                    ->label('Stock mínimo')
                    ->helperText('Opcional — para alertas de reabastecimiento')
                    ->numeric(),
                TextInput::make('precio_unitario')
                    ->label('Precio unitario')
                    ->helperText('Opcional — si el ítem se factura al paciente')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
