<?php

namespace App\Filament\Resources\Quirofanos\Pages;

use App\Filament\Resources\Quirofanos\QuirofanoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuirofanos extends ListRecords
{
    protected static string $resource = QuirofanoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => QuirofanoResource::canCreate()),
        ];
    }
}
