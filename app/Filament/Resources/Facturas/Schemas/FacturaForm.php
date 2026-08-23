<?php

namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->label('Paciente')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->searchable()
                    ->preload(),
                TextInput::make('monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('estado_pago')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'anulado' => 'Anulado',
                    ])
                    ->required()
                    ->default('pendiente'),
                TextInput::make('metodo_pago'),
                DatePicker::make('fecha')
                    ->required(),
            ]);
    }
}
