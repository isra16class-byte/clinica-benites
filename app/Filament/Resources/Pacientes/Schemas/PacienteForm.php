<?php

namespace App\Filament\Resources\Pacientes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PacienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombres')
                    ->required(),
                TextInput::make('apellidos')
                    ->required(),
                TextInput::make('cedula')
                    ->required(),
                DatePicker::make('fecha_nacimiento'),
                TextInput::make('telefono')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('direccion'),
                TextInput::make('sexo'),
            ]);
    }
}
