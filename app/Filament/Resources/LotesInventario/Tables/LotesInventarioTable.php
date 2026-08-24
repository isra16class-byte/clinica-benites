<?php

namespace App\Filament\Resources\LotesInventario\Tables;

use App\Filament\Resources\LotesInventario\LoteInventarioResource;
use App\Models\LoteInventario;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LotesInventarioTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.nombre')
                    ->label('Ítem')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('numero_lote')
                    ->label('Lote')
                    ->searchable(),
                TextColumn::make('fecha_vencimiento')
                    ->label('Vence')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn (LoteInventario $record): string => match (true) {
                        $record->vencido() => 'danger',
                        $record->porVencer() => 'warning',
                        default => 'gray',
                    }),
                // Stock derivado de los movimientos, ver LoteInventario::stockActual().
                TextColumn::make('stock_actual')
                    ->label('Stock actual')
                    ->state(fn (LoteInventario $record): float => $record->stockActual())
                    ->badge()
                    ->color(fn (LoteInventario $record): string => $record->stockActual() > 0 ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_vencimiento')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (LoteInventario $record): bool => LoteInventarioResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
