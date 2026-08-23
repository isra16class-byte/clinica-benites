<?php

namespace App\Filament\Resources\HistoriaClinicas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class HistoriaClinicaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->label('Paciente')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('medico_id')
                    ->relationship('medico', 'nombres')
                    ->label('Médico')
                    ->searchable()
                    ->preload()
                    ->required()
                    // Mismo criterio que en CitaForm: preseleccionar al
                    // médico logueado si está vinculado, evita el error de
                    // registrar una historia clínica a nombre de otro médico.
                    ->default(fn (): ?int => Auth::user()?->medico_id),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->searchable()
                    ->preload(),
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
