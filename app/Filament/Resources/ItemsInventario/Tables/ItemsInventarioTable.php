<?php

namespace App\Filament\Resources\ItemsInventario\Tables;

use App\Filament\Resources\ItemsInventario\ItemInventarioResource;
use App\Models\ItemInventario;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ItemsInventarioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'medicamento' => 'info',
                        'insumo' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable(),
                TextColumn::make('unidad_medida')
                    ->label('Unidad'),
                // Stock actual: no es una columna de BD, se recalcula desde
                // los movimientos (ver ItemInventario::stockActual()).
                TextColumn::make('stock_actual')
                    ->label('Stock actual')
                    ->state(fn (ItemInventario $record): float => $record->stockActual())
                    ->badge()
                    ->color(fn (ItemInventario $record): string => $record->bajoStockMinimo() ? 'danger' : 'success'),
                TextColumn::make('stock_minimo')
                    ->label('Stock mínimo')
                    ->placeholder('—'),
                TextColumn::make('precio_unitario')
                    ->label('Precio')
                    ->money('USD')
                    ->placeholder('—'),
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
                SelectFilter::make('tipo')
                    ->options([
                        'medicamento' => 'Medicamento',
                        'insumo' => 'Insumo',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (ItemInventario $record): bool => ItemInventarioResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
