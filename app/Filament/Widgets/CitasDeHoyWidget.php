<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Citas\CitaResource;
use App\Models\Cita;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CitasDeHoyWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Citas de hoy';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cita::query()
                    ->whereDate('fecha', today())
                    ->orderBy('hora_inicio')
            )
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
