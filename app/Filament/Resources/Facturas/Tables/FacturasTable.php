<?php

namespace App\Filament\Resources\Facturas\Tables;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Models\Factura;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FacturasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nombres')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('numeroComprobante')
                    ->label('N.° comprobante')
                    ->state(fn (Factura $record): string => $record->numeroComprobante() ?? '-')
                    ->toggleable(),
                TextColumn::make('cita.fecha')
                    ->label('Fecha de cita')
                    ->date()
                    ->sortable(),
                TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('estado_pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'pagado' => 'success',
                        'anulado' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('forma_pago')
                    ->label('Forma de pago')
                    ->formatStateUsing(fn (?string $state): string => Factura::FORMAS_PAGO[$state] ?? ($state ?? '-')),
                // Facturación electrónica SRI (MEMORIA.md sección 6): todas
                // las facturas nacen 'no_emitida' mientras el cliente no
                // tenga RUC/establecimiento/punto de emisión/certificado
                // .p12 — ver App\Services\Sri\FacturaSriService (sin probar
                // en este sandbox).
                TextColumn::make('estado_sri')
                    ->label('Estado SRI')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'autorizada' => 'success',
                        'pendiente' => 'warning',
                        'rechazada', 'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Factura::ESTADOS_SRI[$state] ?? $state),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
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
                SelectFilter::make('estado_sri')
                    ->label('Estado SRI')
                    ->options(Factura::ESTADOS_SRI),
            ])
            ->recordActions([
                Action::make('exportarPdf')
                    ->label('PDF')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('gray')
                    ->url(fn (Factura $record): string => route('facturas.pdf', $record))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->visible(fn (Factura $record): bool => FacturaResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
