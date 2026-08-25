<?php

namespace App\Filament\Resources\Internamientos\Pages;

use App\Filament\Resources\Internamientos\InternamientoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInternamientos extends ListRecords
{
    protected static string $resource = InternamientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => InternamientoResource::canCreate()),
        ];
    }
}
