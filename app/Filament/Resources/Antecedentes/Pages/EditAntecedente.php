<?php

namespace App\Filament\Resources\Antecedentes\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Antecedentes\AntecedenteResource;
use App\Models\Antecedente;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAntecedente extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = AntecedenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Antecedente $record): bool => AntecedenteResource::canDelete($record)),
        ];
    }
}
