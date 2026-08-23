<?php

namespace App\Filament\Resources\Pacientes\Pages;

use App\Filament\Resources\Pacientes\PacienteResource;
use App\Models\Paciente;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaciente extends EditRecord
{
    protected static string $resource = PacienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Paciente $record): bool => PacienteResource::canDelete($record))
                ->before(function (Paciente $record, DeleteAction $action) {
                    if ($record->citas()->exists() || $record->historiaClinicas()->exists() || $record->facturas()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar este paciente')
                            ->body('Tiene citas, historias clínicas o facturas asociadas. No se puede eliminar sin perder ese historial.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
