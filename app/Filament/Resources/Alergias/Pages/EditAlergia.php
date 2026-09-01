<?php

namespace App\Filament\Resources\Alergias\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Alergias\AlergiaResource;
use App\Models\Alergia;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlergia extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = AlergiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Alergia $record): bool => AlergiaResource::canDelete($record)),
        ];
    }
}
