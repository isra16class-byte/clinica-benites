<?php

namespace App\Filament\Resources\Quirofanos\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Quirofanos\QuirofanoResource;
use App\Models\Quirofano;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuirofano extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = QuirofanoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Quirofano $record): bool => QuirofanoResource::canDelete($record))
                ->before(function (Quirofano $record, DeleteAction $action) {
                    if ($record->cirugias()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar este quirófano')
                            ->body('Tiene cirugías registradas (programadas o pasadas). No se puede eliminar sin perder ese historial.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
