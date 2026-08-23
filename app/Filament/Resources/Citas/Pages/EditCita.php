<?php

namespace App\Filament\Resources\Citas\Pages;

use App\Filament\Resources\Citas\CitaResource;
use App\Models\Cita;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCita extends EditRecord
{
    protected static string $resource = CitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (Cita $record, DeleteAction $action) {
                    if ($record->historiaClinicas()->exists() || $record->facturas()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar esta cita')
                            ->body('Tiene una historia clínica o factura asociada. No se puede eliminar sin perder ese registro.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
