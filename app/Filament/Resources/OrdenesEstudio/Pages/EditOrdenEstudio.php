<?php

namespace App\Filament\Resources\OrdenesEstudio\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\OrdenesEstudio\OrdenEstudioResource;
use App\Models\OrdenEstudio;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrdenEstudio extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = OrdenEstudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (OrdenEstudio $record): bool => OrdenEstudioResource::canDelete($record)),
        ];
    }
}
