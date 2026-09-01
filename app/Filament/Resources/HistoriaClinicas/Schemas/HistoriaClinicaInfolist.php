<?php

namespace App\Filament\Resources\HistoriaClinicas\Schemas;

use App\Filament\Resources\Alergias\Tables\AlergiasTable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HistoriaClinicaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Destacado por seguridad del paciente (MEMORIA.md sección
                // 8, expediente clínico completo): la alergia se registra
                // por paciente, no por consulta, así que se muestra siempre
                // que el médico está viendo una historia clínica de ese
                // paciente. Solo aparece si tiene alergias registradas.
                Section::make('⚠ Alergias del paciente')
                    ->visible(fn ($record): bool => $record->paciente?->alergias()->exists() ?? false)
                    ->schema([
                        RepeatableEntry::make('paciente.alergias')
                            ->label('')
                            ->schema([
                                TextEntry::make('alergeno')
                                    ->label('Alérgeno')
                                    ->weight('bold'),
                                TextEntry::make('severidad')
                                    ->badge()
                                    ->color(fn (string $state): string => AlergiasTable::colorSeveridad($state))
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                TextEntry::make('reaccion')
                                    ->label('Reacción')
                                    ->placeholder('-'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
                TextEntry::make('paciente.nombres')
                    ->label('Paciente'),
                TextEntry::make('medico.nombres')
                    ->label('Médico'),
                TextEntry::make('cita.fecha')
                    ->label('Fecha de cita')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('motivo_consulta')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('diagnostico')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('tratamiento')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                // Módulo 3 del expediente clínico completo (MEMORIA.md
                // sección 8): solo se muestra si se cargó al menos un
                // signo vital en esta consulta (relación 1 a 1 puede no
                // existir todavía).
                Section::make('Signos vitales')
                    ->visible(fn ($record): bool => $record->signosVitales !== null)
                    ->columns(4)
                    ->schema([
                        TextEntry::make('signosVitales.presion_arterial')
                            ->label('Presión arterial')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.temperatura')
                            ->label('Temperatura')
                            ->suffix(' °C')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.frecuencia_cardiaca')
                            ->label('Frec. cardíaca')
                            ->suffix(' lpm')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.frecuencia_respiratoria')
                            ->label('Frec. respiratoria')
                            ->suffix(' rpm')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.peso')
                            ->suffix(' kg')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.talla')
                            ->suffix(' cm')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.saturacion_oxigeno')
                            ->label('Saturación de O₂')
                            ->suffix('%')
                            ->placeholder('-'),
                        TextEntry::make('signosVitales.notas')
                            ->label('Notas')
                            ->placeholder('-')
                            ->columnSpan(4),
                    ]),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
