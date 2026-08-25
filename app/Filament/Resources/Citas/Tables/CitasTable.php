<?php

namespace App\Filament\Resources\Citas\Tables;

use App\Filament\Resources\Citas\CitaResource;
use App\Models\Cita;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CitasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medico.nombres')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora_inicio')
                    ->time()
                    ->sortable(),
                TextColumn::make('hora_fin')
                    ->time()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => self::colorEstado($state))
                    ->searchable(),
                TextColumn::make('origen')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'emergencia' ? 'danger' : 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('hoy')
                    ->label('Hoy')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereDate('fecha', today())),
                Filter::make('pendientes')
                    ->label('Pendientes')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'pendiente')),
                Filter::make('confirmadas')
                    ->label('Confirmadas')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'confirmada')),
                Filter::make('emergencias')
                    ->label('Emergencias')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('origen', 'emergencia')),
            ])
            ->recordActions([
                ActionGroup::make(self::accionesCambiarEstado())
                    ->label('Cambiar estado')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->visible(fn (Cita $record): bool => CitaResource::canEdit($record)),
                EditAction::make()
                    ->visible(fn (Cita $record): bool => CitaResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }

    /**
     * Un botón por cada estado válido de una cita, para cambiarlo con un
     * clic desde la tabla sin abrir el formulario completo de edición.
     *
     * @return array<Action>
     */
    protected static function accionesCambiarEstado(): array
    {
        $estados = [
            'pendiente' => 'Marcar como pendiente',
            'confirmada' => 'Marcar como confirmada',
            'atendida' => 'Marcar como atendida',
            'cancelada' => 'Marcar como cancelada',
        ];

        return collect($estados)
            ->map(fn (string $label, string $estado): Action => Action::make("estado_{$estado}")
                ->label($label)
                ->color(self::colorEstado($estado))
                ->visible(fn (Cita $record): bool => $record->estado !== $estado)
                ->action(function (Cita $record) use ($estado): void {
                    $record->update(['estado' => $estado]);

                    Notification::make()
                        ->title('Estado de la cita actualizado')
                        ->success()
                        ->send();
                }))
            ->values()
            ->all();
    }

    protected static function colorEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente' => 'gray',
            'confirmada' => 'info',
            'atendida' => 'success',
            'cancelada' => 'danger',
            default => 'gray',
        };
    }
}
