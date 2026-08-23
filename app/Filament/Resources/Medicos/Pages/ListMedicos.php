<?php

namespace App\Filament\Resources\Medicos\Pages;

use App\Filament\Resources\Medicos\MedicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicos extends ListRecords
{
    protected static string $resource = MedicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
