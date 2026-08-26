<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getRolFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }

    protected function getRolFormComponent(): Component
    {
        // Mismo mapeo de etiquetas que UsersTable.php/UserForm.php, para
        // que el rol se vea igual en todo el panel. Solo lectura: el
        // usuario no puede cambiar su propio rol desde su perfil (sigue
        // siendo exclusivo de /admin/users, ver matriz de la sección 10).
        return TextInput::make('rol')
            ->label('Rol')
            ->formatStateUsing(fn (?string $state): string => match ($state) {
                'admin' => 'Administrador',
                'recepcion' => 'Recepción',
                'medico' => 'Médico',
                default => $state ?? '—',
            })
            ->disabled()
            ->dehydrated(false);
    }
}
