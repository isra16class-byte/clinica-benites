<?php

namespace App\Filament\Resources\OrdenesEstudio\Tables;

use App\Filament\Resources\OrdenesEstudio\OrdenEstudioResource;
use App\Models\OrdenEstudio;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrdenesEstudioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (OrdenEstudio $record): string => trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}"))
                    ->searchable(['nombres', 'apellidos'])
                    ->sortable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->searchable(),
                TextColumn::make('medicoSolicitante.nombres')
                    ->label('Solicitante')
                    ->searchable(),
                TextColumn::make('fecha_solicitud')
                    ->label('Solicitud')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'solicitado' => 'gray',
                        'en_proceso' => 'warning',
                        'completado' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_solicitud', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'laboratorio' => 'Laboratorio',
                        'rayos_x' => 'Rayos X',
                        'ecografia' => 'Ecografía',
                        'centro_imagen' => 'Centro de Imagen',
                        'endoscopia_alta' => 'Endoscopía alta',
                        'endoscopia_baja' => 'Endoscopía baja',
                        'gastroenterologia' => 'Centro de Gastroenterología',
                        'procedimiento_ambulatorio' => 'Procedimiento ambulatorio',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'solicitado' => 'Solicitado',
                        'en_proceso' => 'En proceso',
                        'completado' => 'Completado',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (OrdenEstudio $record): bool => OrdenEstudioResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
