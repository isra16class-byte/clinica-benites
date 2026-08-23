<?php

namespace App\Filament\Resources\Citas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class CitaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('paciente_id')
                    ->required()
                    ->numeric(),
                TextInput::make('medico_id')
                    ->required()
                    ->numeric(),
                TextInput::make('area_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha')
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->required(),
                TimePicker::make('hora_fin')
                    ->required(),
                TextInput::make('estado')
                    ->required()
                    ->default('pendiente'),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
