<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    // Mismo patrón que la validación de cédula única en Pacientes:
                    // sin esto, un email repetido mostraría el error crudo de MySQL
                    // en vez de un mensaje de validación claro.
                    ->unique(table: 'users', column: 'email', ignoreRecord: true),
                Select::make('rol')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'recepcion' => 'Recepción',
                        'medico' => 'Médico',
                    ])
                    ->required()
                    ->default('recepcion'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Al editar un usuario existente, déjala en blanco para no cambiar la contraseña actual.'),
            ]);
    }
}
