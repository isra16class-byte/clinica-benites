<?php

namespace App\Filament\Resources\HistoriaClinicas\Pages;

use App\Filament\Resources\HistoriaClinicas\HistoriaClinicaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHistoriaClinica extends ViewRecord
{
    protected static string $resource = HistoriaClinicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
