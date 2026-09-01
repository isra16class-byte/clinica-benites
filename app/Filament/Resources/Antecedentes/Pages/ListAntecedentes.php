<?php

namespace App\Filament\Resources\Antecedentes\Pages;

use App\Filament\Resources\Antecedentes\AntecedenteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAntecedentes extends ListRecords
{
    protected static string $resource = AntecedenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => AntecedenteResource::canCreate()),
        ];
    }
}
