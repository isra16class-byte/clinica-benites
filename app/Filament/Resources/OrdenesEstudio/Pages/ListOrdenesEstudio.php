<?php

namespace App\Filament\Resources\OrdenesEstudio\Pages;

use App\Filament\Resources\OrdenesEstudio\OrdenEstudioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrdenesEstudio extends ListRecords
{
    protected static string $resource = OrdenEstudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => OrdenEstudioResource::canCreate()),
        ];
    }
}
