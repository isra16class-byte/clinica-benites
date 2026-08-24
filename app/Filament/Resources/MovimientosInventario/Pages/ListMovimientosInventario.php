<?php

namespace App\Filament\Resources\MovimientosInventario\Pages;

use App\Filament\Resources\MovimientosInventario\MovimientoInventarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMovimientosInventario extends ListRecords
{
    protected static string $resource = MovimientoInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => MovimientoInventarioResource::canCreate()),
        ];
    }
}
