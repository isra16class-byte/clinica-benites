<?php

namespace App\Filament\Resources\Cirugias\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Models\Cirugia;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCirugia extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = CirugiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Cirugia $record): bool => CirugiaResource::canDelete($record)),
        ];
    }
}
