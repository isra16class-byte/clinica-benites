<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rol')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'recepcion' => 'info',
                        'medico' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'recepcion' => 'Recepción',
                        'medico' => 'Médico',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('medico.nombres')
                    ->label('Médico vinculado')
                    ->formatStateUsing(fn ($state, User $record): ?string => $record->medico
                        ? trim("{$record->medico->nombres} {$record->medico->apellidos}")
                        : null)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtro rápido por rol (admin/recepción/médico). Se usa
                // SelectFilter en vez de 3 Filter->toggle() como en Citas
                // porque los roles son mutuamente excluyentes (un usuario
                // solo tiene uno), a diferencia de "Hoy"/"Pendientes" en
                // Citas, que sí se pueden combinar entre sí.
                SelectFilter::make('rol')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'recepcion' => 'Recepción',
                        'medico' => 'Médico',
                    ]),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                EditAction::make()
                    ->visible(fn (User $record): bool => UserResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false)
                        // A diferencia del resto de Resources, aquí sí puede
                        // haber un registro "peligroso" dentro de una selección
                        // masiva: la propia cuenta del admin que está borrando.
                        // canDelete() ya lo bloquea en el borrado individual
                        // (vía ->visible() en EditAction/DeleteAction), pero
                        // DeleteBulkAction actúa sobre varios registros a la
                        // vez sin ese chequeo por fila, así que se valida aquí.
                        ->before(function (Collection $records, DeleteBulkAction $action) {
                            if ($records->contains('id', Auth::id())) {
                                Notification::make()
                                    ->title('No puedes eliminar tu propio usuario')
                                    ->body('Quita tu cuenta de la selección e inténtalo de nuevo.')
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }
}
