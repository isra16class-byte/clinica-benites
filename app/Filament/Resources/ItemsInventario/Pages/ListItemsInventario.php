<?php

namespace App\Filament\Resources\ItemsInventario\Pages;

use App\Filament\Resources\ItemsInventario\ItemInventarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemsInventario extends ListRecords
{
    protected static string $resource = ItemInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => ItemInventarioResource::canCreate()),
        ];
    }
}
