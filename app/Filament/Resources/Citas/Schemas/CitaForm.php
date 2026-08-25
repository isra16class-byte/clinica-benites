<?php

namespace App\Filament\Resources\Citas\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
                    ->required()
                    // Si quien crea la cita es un médico vinculado a su
                    // propio registro (users.medico_id), se preselecciona a
                    // sí mismo para evitar el error de agendarle una cita a
                    // otro médico por descuido. Sigue siendo editable, y no
                    // afecta a admin/recepción (no tienen medico_id).
                    ->default(fn (): ?int => Auth::user()?->medico_id),
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
                // Origen/prioridad (sección 6.2 de MEMORIA.md, grupo 4 —
                // Emergencias): en vez de un modelo nuevo, una emergencia
                // que no requiere internamiento queda registrada acá mismo,
                // como una Cita con origen "emergencia" y su prioridad ESI.
                Select::make('origen')
                    ->label('Origen')
                    ->options([
                        'programada' => 'Programada',
                        'emergencia' => 'Emergencia',
                    ])
                    ->required()
                    ->default('programada')
                    ->live(),
                Select::make('prioridad')
                    ->label('Prioridad (triage)')
                    ->helperText('Escala ESI: 1 = reanimación inmediata, 5 = no urgente')
                    ->options([
                        'esi_1' => 'ESI 1 — Reanimación',
                        'esi_2' => 'ESI 2 — Emergencia',
                        'esi_3' => 'ESI 3 — Urgente',
                        'esi_4' => 'ESI 4 — Menos urgente',
                        'esi_5' => 'ESI 5 — No urgente',
                    ])
                    ->visible(fn ($get): bool => $get('origen') === 'emergencia'),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
