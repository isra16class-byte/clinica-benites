<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Medico;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->default('recepcion')
                    ->live(),
                Select::make('medico_id')
                    ->relationship('medico', 'nombres')
                    ->getOptionLabelFromRecordUsing(fn (Medico $record): string => "{$record->nombres} {$record->apellidos}")
                    ->label('Médico vinculado')
                    ->searchable(['nombres', 'apellidos'])
                    ->preload()
                    // Solo tiene sentido (y solo se muestra) cuando el rol es
                    // medico: conecta este usuario con su registro en la
                    // tabla `medicos`, para poder filtrar "mis pacientes" en
                    // Citas e Historias Clínicas (ver MEMORIA.md sección 10).
                    ->visible(fn (Get $get): bool => $get('rol') === 'medico')
                    ->helperText('Necesario para que este usuario solo vea sus propias citas e historias clínicas al iniciar sesión.'),
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
