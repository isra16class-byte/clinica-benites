<?php

namespace App\Filament\Resources\MovimientosInventario\Tables;

use App\Filament\Resources\MovimientosInventario\MovimientoInventarioResource;
use App\Models\MovimientoInventario;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MovimientosInventarioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lote.item.nombre')
                    ->label('Ítem')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lote.numero_lote')
                    ->label('Lote')
                    ->searchable(),
                TextColumn::make('tipo_movimiento')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success',
                        'salida' => 'danger',
                        'traslado' => 'info',
                        'ajuste' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable(),
                TextColumn::make('cantidad')
                    ->sortable(),
                TextColumn::make('area_origen')
                    ->label('Desde')
                    ->placeholder('—'),
                TextColumn::make('area_destino')
                    ->label('Hacia')
                    ->placeholder('—'),
                TextColumn::make('fecha_hora')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('usuario.name')
                    ->label('Registrado por'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_hora', 'desc')
            ->filters([
                SelectFilter::make('tipo_movimiento')
                    ->label('Tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'traslado' => 'Traslado',
                        'ajuste' => 'Ajuste',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (MovimientoInventario $record): bool => MovimientoInventarioResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
