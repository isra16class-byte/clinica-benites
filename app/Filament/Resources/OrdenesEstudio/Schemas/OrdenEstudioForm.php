<?php

namespace App\Filament\Resources\OrdenesEstudio\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrdenEstudioForm
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
                Select::make('medico_solicitante_id')
                    ->relationship('medicoSolicitante', 'nombres')
                    ->label('Médico solicitante')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn (): ?int => Auth::user()?->medico_id),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->helperText('Opcional')
                    ->searchable()
                    ->preload(),
                Select::make('tipo')
                    ->options([
                        'laboratorio' => 'Laboratorio',
                        'rayos_x' => 'Rayos X',
                        'ecografia' => 'Ecografía',
                        'centro_imagen' => 'Centro de Imagen',
                        'endoscopia_alta' => 'Endoscopía alta',
                        'endoscopia_baja' => 'Endoscopía baja',
                        'gastroenterologia' => 'Centro de Gastroenterología',
                        'procedimiento_ambulatorio' => 'Procedimiento ambulatorio',
                    ])
                    ->required(),
                DateTimePicker::make('fecha_solicitud')
                    ->label('Fecha de solicitud')
                    ->default(now())
                    ->required(),
                DateTimePicker::make('fecha_realizacion')
                    ->label('Fecha de realización')
                    ->helperText('Vacío mientras no se realiza el estudio'),
                Select::make('estado')
                    ->options([
                        'solicitado' => 'Solicitado',
                        'en_proceso' => 'En proceso',
                        'completado' => 'Completado',
                    ])
                    ->required()
                    ->default('solicitado'),
                Textarea::make('resultado_texto')
                    ->label('Resultado (texto)')
                    ->columnSpanFull(),
                // Adjunto opcional (supuesto razonable, sección 6.2 de
                // MEMORIA.md, grupo 3 — no evaluado todavía si conviene
                // storage externo tipo S3, por ahora disco local de Sail).
                FileUpload::make('resultado_archivo')
                    ->label('Resultado (archivo adjunto)')
                    ->helperText('Opcional — ej. radiografía escaneada, PDF de laboratorio. Acepta PDF e imágenes (JPG/PNG/WEBP).')
                    ->disk('public')
                    ->directory('estudios')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
