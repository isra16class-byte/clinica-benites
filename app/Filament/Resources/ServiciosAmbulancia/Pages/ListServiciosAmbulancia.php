<?php

namespace App\Filament\Resources\ServiciosAmbulancia\Pages;

use App\Filament\Resources\ServiciosAmbulancia\ServicioAmbulanciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiciosAmbulancia extends ListRecords
{
    protected static string $resource = ServicioAmbulanciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => ServicioAmbulanciaResource::canCreate()),
        ];
    }
}
