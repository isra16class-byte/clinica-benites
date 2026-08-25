<?php

namespace App\Filament\Resources\ServiciosAmbulancia\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServicioAmbulanciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->getOptionLabelFromRecordUsing(fn (Paciente $record): string => "{$record->nombres} {$record->apellidos}")
                    ->label('Paciente')
                    ->helperText('Opcional — puede no haber un paciente registrado todavía (ej. traslado desde una emergencia externa)')
                    ->searchable(['nombres', 'apellidos', 'cedula'])
                    ->preload(),
                TextInput::make('origen')
                    ->required(),
                TextInput::make('destino')
                    ->required(),
                DateTimePicker::make('fecha_hora')
                    ->default(now())
                    ->required(),
                TextInput::make('motivo')
                    ->helperText('Opcional'),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
