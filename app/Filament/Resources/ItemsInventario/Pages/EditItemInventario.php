<?php

namespace App\Filament\Resources\ItemsInventario\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\ItemsInventario\ItemInventarioResource;
use App\Models\ItemInventario;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditItemInventario extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = ItemInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (ItemInventario $record): bool => ItemInventarioResource::canDelete($record))
                ->before(function (ItemInventario $record, DeleteAction $action) {
                    if ($record->lotes()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar este ítem')
                            ->body('Tiene lotes registrados. Elimina primero esos lotes (y sus movimientos) si de verdad ya no se usa.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
