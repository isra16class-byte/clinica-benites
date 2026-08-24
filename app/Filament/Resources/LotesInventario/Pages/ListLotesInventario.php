<?php

namespace App\Filament\Resources\LotesInventario\Pages;

use App\Filament\Resources\LotesInventario\LoteInventarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLotesInventario extends ListRecords
{
    protected static string $resource = LoteInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => LoteInventarioResource::canCreate()),
        ];
    }
}
