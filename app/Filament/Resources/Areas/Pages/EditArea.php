<?php

namespace App\Filament\Resources\Areas\Pages;

use App\Filament\Resources\Areas\AreaResource;
use App\Models\Area;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArea extends EditRecord
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (Area $record, DeleteAction $action) {
                    if ($record->medicos()->exists() || $record->citas()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar esta área')
                            ->body('Tiene médicos o citas asociadas. Reasigna o elimina esos registros primero.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
