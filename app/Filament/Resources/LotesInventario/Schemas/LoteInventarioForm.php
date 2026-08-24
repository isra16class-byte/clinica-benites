<?php

namespace App\Filament\Resources\LotesInventario\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LoteInventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'nombre')
                    ->label('Ítem')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('numero_lote')
                    ->label('Número de lote')
                    ->required(),
                DatePicker::make('fecha_vencimiento')
                    ->label('Fecha de vencimiento')
                    ->required(),
            ]);
    }
}
