<?php

namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Models\Factura;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditFactura extends EditRecord
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportarPdf')
                ->label('Exportar PDF')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->url(fn (Factura $record): string => route('facturas.pdf', $record))
                ->openUrlInNewTab(),
            DeleteAction::make()
                ->visible(fn (Factura $record): bool => FacturaResource::canDelete($record)),
        ];
    }
}
