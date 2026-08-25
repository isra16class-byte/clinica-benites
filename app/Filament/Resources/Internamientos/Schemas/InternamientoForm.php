<?php

namespace App\Filament\Resources\Internamientos\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class InternamientoForm
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
                Select::make('cama_id')
                    ->relationship('cama', 'numero')
                    ->label('Cama')
                    // No se valida acá que la cama esté libre — supuesto
                    // razonable (sección 6.2 de MEMORIA.md): el personal
                    // revisa el estado en /admin/camas antes de asignarla.
                    // Si la clínica confirma que hace falta bloquear la
                    // selección de camas ocupadas, es un ajuste puntual acá.
                    ->helperText('Verifica en el listado de Camas que esté libre antes de asignarla.')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('medico_id')
                    ->relationship('medico', 'nombres')
                    ->label('Médico responsable')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn (): ?int => Auth::user()?->medico_id),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->helperText('Opcional — si el ingreso viene de una cita ya agendada')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('fecha_ingreso')
                    ->label('Fecha de ingreso')
                    ->default(now())
                    ->required(),
                DateTimePicker::make('fecha_alta')
                    ->label('Fecha de alta')
                    ->helperText('Vacío mientras el paciente sigue internado'),
                Select::make('origen')
                    ->label('Origen')
                    ->options([
                        'programado' => 'Programado',
                        'emergencia' => 'Emergencia',
                    ])
                    ->required()
                    ->default('programado')
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
                Textarea::make('motivo')
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
