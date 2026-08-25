<?php

namespace App\Filament\Resources\ServiciosAmbulancia\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\ServiciosAmbulancia\ServicioAmbulanciaResource;
use App\Models\ServicioAmbulancia;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServicioAmbulancia extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = ServicioAmbulanciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (ServicioAmbulancia $record): bool => ServicioAmbulanciaResource::canDelete($record)),
        ];
    }
}
