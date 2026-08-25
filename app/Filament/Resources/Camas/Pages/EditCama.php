<?php

namespace App\Filament\Resources\Camas\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Camas\CamaResource;
use App\Models\Cama;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCama extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = CamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Cama $record): bool => CamaResource::canDelete($record))
                ->before(function (Cama $record, DeleteAction $action) {
                    if ($record->internamientos()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar esta cama')
                            ->body('Tiene internamientos registrados (activos o pasados). No se puede eliminar sin perder ese historial.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
