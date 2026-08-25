<?php

namespace App\Filament\Resources\Cirugias\Schemas;

use App\Models\Medico;
use App\Models\Paciente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class CirugiaForm
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
                    ->required(),
                Select::make('quirofano_id')
                    ->relationship('quirofano', 'numero')
                    ->label('Quirófano')
                    ->helperText('Verifica en el listado de Quirófanos que esté libre antes de asignarlo.')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('medico_principal_id')
                    ->relationship('medicoPrincipal', 'nombres')
                    ->label('Cirujano principal')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->helperText('Opcional — no toda cirugía nace de una cita ya agendada')
                    ->searchable()
                    ->preload(),
                DatePicker::make('fecha')
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->required(),
                TimePicker::make('hora_fin'),
                TextInput::make('tipo_cirugia')
                    ->label('Tipo de cirugía')
                    ->required(),
                Select::make('estado')
                    ->options([
                        'programada' => 'Programada',
                        'en_curso' => 'En curso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->required()
                    ->default('programada'),
                // Médicos adicionales (anestesiólogo, ayudantes) aparte del
                // cirujano principal — punto abierto de la propuesta
                // original (sección 6.2 de MEMORIA.md, grupo 2): una
                // cirugía suele involucrar más de un médico.
                Repeater::make('medicosAdicionales')
                    ->label('Médicos adicionales')
                    ->addActionLabel('Agregar médico')
                    ->schema([
                        Select::make('medico_id')
                            ->label('Médico')
                            ->options(fn (): array => Medico::query()
                                ->get()
                                ->mapWithKeys(fn (Medico $medico): array => [$medico->id => "{$medico->nombres} {$medico->apellidos}"])
                                ->toArray())
                            ->searchable()
                            ->required(),
                        TextInput::make('rol')
                            ->label('Rol')
                            ->placeholder('ej. Anestesiólogo, Ayudante'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
