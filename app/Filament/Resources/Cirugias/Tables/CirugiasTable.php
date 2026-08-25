<?php

namespace App\Filament\Resources\Cirugias\Tables;

use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Models\Cirugia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CirugiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (Cirugia $record): string => trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}"))
                    ->searchable(['nombres', 'apellidos'])
                    ->sortable(),
                TextColumn::make('tipo_cirugia')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('quirofano.numero')
                    ->label('Quirófano')
                    ->sortable(),
                TextColumn::make('medicoPrincipal.nombres')
                    ->label('Cirujano principal')
                    ->searchable(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora_inicio')
                    ->time()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'programada' => 'gray',
                        'en_curso' => 'warning',
                        'completada' => 'success',
                        'cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_curso' => 'En curso',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'programada' => 'Programada',
                        'en_curso' => 'En curso',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Cirugia $record): bool => CirugiaResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
