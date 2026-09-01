<?php

namespace App\Filament\Resources\Antecedentes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AntecedenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->nombres} {$record->apellidos}")
                    ->label('Paciente')
                    ->searchable(['nombres', 'apellidos', 'cedula'])
                    ->preload()
                    ->required()
                    // Oculto en edición, mismo criterio que AlergiaForm: el
                    // paciente no cambia una vez creado el registro. En el
                    // relation manager de la ficha del paciente este campo
                    // directamente no se muestra (ver
                    // AntecedentesRelationManager).
                    ->visibleOn('create'),
                Select::make('categoria')
                    ->options([
                        'personal' => 'Personal',
                        'quirurgico' => 'Quirúrgico',
                        'familiar' => 'Familiar',
                        'habito' => 'Hábito',
                    ])
                    ->required(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->helperText('Ej. "Diabetes tipo 2", "Apendicectomía (2015)", "Madre con hipertensión", "Fuma 10 cigarrillos/día".')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
