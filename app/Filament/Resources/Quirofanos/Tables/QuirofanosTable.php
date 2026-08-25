<?php

namespace App\Filament\Resources\Quirofanos\Tables;

use App\Filament\Resources\Quirofanos\QuirofanoResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class QuirofanosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'libre' => 'success',
                        'preparacion' => 'warning',
                        'en_cirugia' => 'danger',
                        'limpieza' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'libre' => 'Libre',
                        'preparacion' => 'En preparación',
                        'en_cirugia' => 'En cirugía',
                        'limpieza' => 'En limpieza',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'libre' => 'Libre',
                        'preparacion' => 'En preparación',
                        'en_cirugia' => 'En cirugía',
                        'limpieza' => 'En limpieza',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => QuirofanoResource::canEdit(null)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
