<?php

namespace App\Filament\Resources\Internamientos\Tables;

use App\Filament\Resources\Internamientos\InternamientoResource;
use App\Models\Internamiento;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InternamientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (Internamiento $record): string => trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}"))
                    ->searchable(['nombres', 'apellidos'])
                    ->sortable(),
                TextColumn::make('cama.numero')
                    ->label('Cama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medico.nombres')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_ingreso')
                    ->label('Ingreso')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fecha_alta')
                    ->label('Alta')
                    ->dateTime()
                    ->placeholder('Internado')
                    ->sortable(),
                TextColumn::make('origen')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'emergencia' ? 'danger' : 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_ingreso', 'desc')
            ->filters([
                Filter::make('activos')
                    ->label('Internados ahora')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNull('fecha_alta')),
                SelectFilter::make('origen')
                    ->options([
                        'programado' => 'Programado',
                        'emergencia' => 'Emergencia',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Internamiento $record): bool => InternamientoResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
