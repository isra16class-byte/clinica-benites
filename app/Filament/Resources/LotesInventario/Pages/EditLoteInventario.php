<?php

namespace App\Filament\Resources\LotesInventario\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\LotesInventario\LoteInventarioResource;
use App\Models\LoteInventario;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLoteInventario extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = LoteInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (LoteInventario $record): bool => LoteInventarioResource::canDelete($record))
                ->before(function (LoteInventario $record, DeleteAction $action) {
                    if ($record->movimientos()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar este lote')
                            ->body('Tiene movimientos registrados. Un lote con historial de movimientos no se puede borrar, por trazabilidad.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
