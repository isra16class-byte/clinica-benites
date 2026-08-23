<?php

namespace App\Filament\Resources\HistoriaClinicas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HistoriaClinicaForm
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
                TextInput::make('cita_id')
                    ->numeric(),
                Textarea::make('motivo_consulta')
                    ->columnSpanFull(),
                Textarea::make('diagnostico')
                    ->columnSpanFull(),
                Textarea::make('tratamiento')
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
