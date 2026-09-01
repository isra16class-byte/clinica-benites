<?php

namespace App\Filament\Resources\Alergias\Pages;

use App\Filament\Resources\Alergias\AlergiaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlergias extends ListRecords
{
    protected static string $resource = AlergiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => AlergiaResource::canCreate()),
        ];
    }
}
