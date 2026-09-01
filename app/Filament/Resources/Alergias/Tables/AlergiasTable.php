<?php

namespace App\Filament\Resources\Alergias\Tables;

use App\Filament\Resources\Alergias\AlergiaResource;
use App\Models\Alergia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AlergiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (Alergia $record): string => trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}"))
                    ->searchable(['nombres', 'apellidos']),
                TextColumn::make('alergeno')
                    ->label('Alérgeno')
                    ->searchable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('severidad')
                    ->badge()
                    ->color(fn (string $state): string => self::colorSeveridad($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('reaccion')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'medicamento' => 'Medicamento',
                        'alimento' => 'Alimento',
                        'otro' => 'Otro',
                    ]),
                SelectFilter::make('severidad')
                    ->options([
                        'leve' => 'Leve',
                        'moderada' => 'Moderada',
                        'severa' => 'Severa',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Alergia $record): bool => AlergiaResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }

    public static function colorSeveridad(string $severidad): string
    {
        return match ($severidad) {
            'severa' => 'danger',
            'moderada' => 'warning',
            'leve' => 'gray',
            default => 'gray',
        };
    }
}
