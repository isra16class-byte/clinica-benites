<?php

namespace App\Filament\Resources\HistoriaClinicas\Pages;

use App\Filament\Resources\HistoriaClinicas\HistoriaClinicaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHistoriaClinica extends EditRecord
{
    protected static string $resource = HistoriaClinicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
