<?php

namespace App\Filament\Resources\HistoriaClinicas\Pages;

use App\Filament\Resources\HistoriaClinicas\HistoriaClinicaResource;
use App\Models\HistoriaClinica;
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
            DeleteAction::make()
                ->visible(fn (HistoriaClinica $record): bool => HistoriaClinicaResource::canDelete($record)),
        ];
    }
}
