<?php

namespace App\Filament\Resources\HistoriaClinicas\Pages;

use App\Filament\Resources\HistoriaClinicas\HistoriaClinicaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHistoriaClinicas extends ListRecords
{
    protected static string $resource = HistoriaClinicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => HistoriaClinicaResource::canCreate()),
        ];
    }
}
