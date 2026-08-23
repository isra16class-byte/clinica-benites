<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Citas\CitaResource;
use App\Models\Cita;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CitasDeHoyWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Citas de hoy';

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Cita::query()
                    ->whereDate('fecha', today())
                    ->orderBy('hora_inicio');

                // Mismo filtro "mis pacientes" que CitaResource: un médico
                // vinculado (users.medico_id) solo ve sus propias citas de
                // hoy en el widget del dashboard (ver MEMORIA.md sección 10).
                $user = Auth::user();

                if ($user?->isMedico() && $user->medico_id) {
                    $query->where('medico_id', $user->medico_id);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('hora_inicio')
                    ->label('Hora')
                    ->time()
                    ->sortable(),
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->searchable(),
                TextColumn::make('medico.nombres')
                    ->label('Médico')
                    ->searchable(),
                TextColumn::make('area.nombre')
                    ->label('Área'),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'confirmada' => 'info',
                        'atendida' => 'success',
                        'cancelada' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Cita $record): bool => CitaResource::canEdit($record))
                    ->url(fn (Cita $record): string => CitaResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('No hay citas para hoy')
            ->emptyStateDescription('Las citas que se agenden con fecha de hoy van a aparecer aquí.');
    }
}
