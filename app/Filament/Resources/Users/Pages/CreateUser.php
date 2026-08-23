<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Cinturón de seguridad extra (además del afterStateUpdated en
     * UserForm): si por cualquier motivo llegara un medico_id junto con
     * un rol distinto de "medico", se limpia aquí antes de guardar, para
     * no dejar nunca un usuario no-médico con un médico vinculado.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['rol'] ?? null) !== 'medico') {
            $data['medico_id'] = null;
        }

        return $data;
    }
}
