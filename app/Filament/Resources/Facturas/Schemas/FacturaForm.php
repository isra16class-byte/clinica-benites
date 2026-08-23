<?php

namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('paciente_id')
                    ->required()
                    ->numeric(),
                TextInput::make('cita_id')
                    ->numeric(),
                TextInput::make('monto')
                    ->required()
                    ->numeric(),
                TextInput::make('estado_pago')
                    ->required()
                    ->default('pendiente'),
                TextInput::make('metodo_pago'),
                DatePicker::make('fecha')
                    ->required(),
            ]);
    }
}
