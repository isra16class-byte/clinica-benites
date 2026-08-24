<?php

namespace App\Filament\Resources\Medicos\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Medicos\MedicoResource;
use App\Models\Medico;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMedico extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = MedicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Medico $record): bool => MedicoResource::canDelete($record))
                ->before(function (Medico $record, DeleteAction $action) {
                    if ($record->citas()->exists() || $record->historiaClinicas()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar este médico')
                            ->body('Tiene citas o historias clínicas asociadas. No se puede eliminar sin perder ese historial.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
