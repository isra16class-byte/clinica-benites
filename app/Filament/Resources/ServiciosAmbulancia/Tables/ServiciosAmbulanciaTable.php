<?php

namespace App\Filament\Resources\ServiciosAmbulancia\Tables;

use App\Filament\Resources\ServiciosAmbulancia\ServicioAmbulanciaResource;
use App\Models\ServicioAmbulancia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ServiciosAmbulanciaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->formatStateUsing(fn (ServicioAmbulancia $record): string => $record->paciente ? trim("{$record->paciente->nombres} {$record->paciente->apellidos}") : '—')
                    ->searchable(['nombres', 'apellidos']),
                TextColumn::make('origen')
                    ->searchable(),
                TextColumn::make('destino')
                    ->searchable(),
                TextColumn::make('fecha_hora')
                    ->label('Fecha y hora')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('motivo')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_hora', 'desc')
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => ServicioAmbulanciaResource::canEdit(null)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
