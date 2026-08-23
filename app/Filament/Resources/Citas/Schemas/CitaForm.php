<?php

namespace App\Filament\Resources\Citas\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class CitaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->getOptionLabelFromRecordUsing(fn (Paciente $record): string => "{$record->nombres} {$record->apellidos}")
                    ->label('Paciente')
                    ->searchable(['nombres', 'apellidos', 'cedula'])
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('nombres')
                            ->required(),
                        TextInput::make('apellidos')
                            ->required(),
                        TextInput::make('cedula')
                            ->required()
                            ->unique(table: 'pacientes', column: 'cedula'),
                        DatePicker::make('fecha_nacimiento'),
                        TextInput::make('telefono')
                            ->tel(),
                        TextInput::make('email')
                            ->email(),
                        TextInput::make('direccion'),
                        TextInput::make('sexo'),
                    ])
                    ->createOptionModalHeading('Crear paciente nuevo'),
                Select::make('medico_id')
                    ->relationship('medico', 'nombres')
                    ->label('Médico')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('area_id')
                    ->relationship('area', 'nombre')
                    ->label('Área')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('fecha')
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->required(),
                TimePicker::make('hora_fin')
                    ->required(),
                Select::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                        'atendida' => 'Atendida',
                    ])
                    ->required()
                    ->default('pendiente'),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
