<?php

namespace App\Filament\Resources\Camas\Tables;

use App\Filament\Resources\Camas\CamaResource;
use App\Models\Cama;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CamasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hospitalizacion' => 'info',
                        'uci' => 'danger',
                        'ucin' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hospitalizacion' => 'Hospitalización',
                        'uci' => 'UCI',
                        'ucin' => 'UCIN',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('piso')
                    ->placeholder('—'),
                // Estado: no es columna de BD, se deriva de si tiene un
                // internamiento activo (ver Cama::ocupada()).
                TextColumn::make('estado')
                    ->label('Estado')
                    ->state(fn (Cama $record): string => $record->ocupada() ? 'Ocupada' : 'Libre')
                    ->badge()
                    ->color(fn (Cama $record): string => $record->ocupada() ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'hospitalizacion' => 'Hospitalización',
                        'uci' => 'UCI',
                        'ucin' => 'UCIN',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => CamaResource::canEdit(null)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
