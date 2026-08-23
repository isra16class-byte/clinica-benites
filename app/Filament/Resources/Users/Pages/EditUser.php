<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
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
}
