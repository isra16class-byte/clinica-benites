<?php

namespace App\Filament\Resources\Internamientos\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Internamientos\InternamientoResource;
use App\Models\Internamiento;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInternamiento extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = InternamientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Internamiento $record): bool => InternamientoResource::canDelete($record)),
        ];
    }
}
