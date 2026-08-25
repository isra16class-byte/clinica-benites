<?php

namespace App\Filament\Resources\Camas\Pages;

use App\Filament\Resources\Camas\CamaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCamas extends ListRecords
{
    protected static string $resource = CamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => CamaResource::canCreate()),
        ];
    }
}
