<?php

namespace App\Filament\Resources\HistoriaClinicas\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

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
                    ->required()
                    // ->live() para que el aviso de alergias de abajo se
                    // actualice al cambiar de paciente (seguridad primero,
                    // ver MEMORIA.md sección 8).
                    ->live(),
                Placeholder::make('alergias_aviso')
                    ->label('')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => filled($get('paciente_id')) && Paciente::find($get('paciente_id'))?->alergias()->exists())
                    ->content(function (Get $get): HtmlString {
                        $alergias = Paciente::find($get('paciente_id'))?->alergias ?? collect();

                        $lista = $alergias
                            ->map(fn ($a): string => e("{$a->alergeno} ({$a->severidad})"))
                            ->implode(', ');

                        return new HtmlString(
                            '<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:0.5rem;font-weight:600;">⚠ Alergias registradas: '.$lista.'</div>'
                        );
                    }),
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
