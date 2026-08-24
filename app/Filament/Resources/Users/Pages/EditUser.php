<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // canDelete() ya excluye la propia cuenta del usuario logueado
            // (ver UserResource::canDelete()), así que el botón simplemente
            // no aparece al editar su propio usuario.
            DeleteAction::make()
                ->visible(fn (User $record): bool => UserResource::canDelete($record)),
        ];
    }

    /**
     * Mismo cinturón de seguridad que en CreateUser: si el rol guardado no
     * es "medico", medico_id se limpia aquí antes de guardar, además del
     * afterStateUpdated() en UserForm que ya lo hace al cambiar el Select.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['rol'] ?? null) !== 'medico') {
            $data['medico_id'] = null;
        }

        return $data;
    }
}
