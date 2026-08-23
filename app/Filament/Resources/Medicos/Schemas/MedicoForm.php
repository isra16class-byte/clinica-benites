<?php

namespace App\Filament\Resources\Medicos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombres')
                    ->required(),
                TextInput::make('apellidos')
                    ->required(),
                TextInput::make('area_id')
                    ->required()
                    ->numeric(),
                TextInput::make('telefono')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
            ]);
    }
}
