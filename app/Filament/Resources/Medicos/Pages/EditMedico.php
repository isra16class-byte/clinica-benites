<?php

namespace App\Filament\Resources\Medicos\Pages;

use App\Filament\Resources\Medicos\MedicoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedico extends EditRecord
{
    protected static string $resource = MedicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
