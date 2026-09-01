<?php

namespace App\Filament\Resources\Alergias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AlergiaForm
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
                    // Oculto en edición: el paciente no cambia una vez creado
                    // el registro (evita reasignar una alergia a otra persona
                    // por error). En el relation manager de la ficha del
                    // paciente este campo directamente no se muestra (ver
                    // AlergiasRelationManager).
                    ->visibleOn('create'),
                TextInput::make('alergeno')
                    ->label('Alérgeno')
                    ->helperText('Qué causa la alergia, ej. "Penicilina", "Maní".')
                    ->required(),
                Select::make('tipo')
                    ->options([
                        'medicamento' => 'Medicamento',
                        'alimento' => 'Alimento',
                        'otro' => 'Otro',
                    ])
                    ->required(),
                Select::make('severidad')
                    ->options([
                        'leve' => 'Leve',
                        'moderada' => 'Moderada',
                        'severa' => 'Severa',
                    ])
                    ->required(),
                Textarea::make('reaccion')
                    ->label('Reacción')
                    ->helperText('Qué reacción produce, ej. ronchas, hinchazón, anafilaxia.')
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
