<?php

namespace App\Filament\Resources\Antecedentes\Tables;

use App\Filament\Resources\Antecedentes\AntecedenteResource;
use App\Models\Antecedente;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AntecedentesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (Antecedente $record): string => trim("{$record->paciente?->nombres} {$record->paciente?->apellidos}"))
                    ->searchable(['nombres', 'apellidos']),
                TextColumn::make('categoria')
                    ->badge()
                    ->color(fn (string $state): string => self::colorCategoria($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('notas')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->options([
                        'personal' => 'Personal',
                        'quirurgico' => 'Quirúrgico',
                        'familiar' => 'Familiar',
                        'habito' => 'Hábito',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Antecedente $record): bool => AntecedenteResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }

    public static function colorCategoria(string $categoria): string
    {
        return match ($categoria) {
            'personal' => 'info',
            'quirurgico' => 'warning',
            'familiar' => 'gray',
            'habito' => 'danger',
            default => 'gray',
        };
    }
}
