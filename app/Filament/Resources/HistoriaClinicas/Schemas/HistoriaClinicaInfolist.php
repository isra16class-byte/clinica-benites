<?php

namespace App\Filament\Resources\HistoriaClinicas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HistoriaClinicaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
