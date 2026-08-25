<?php

namespace App\Filament\Resources\Camas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CamaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero')
                    ->required()
                    ->unique(table: 'camas', column: 'numero', ignoreRecord: true),
                Select::make('tipo')
                    ->options([
                        'hospitalizacion' => 'Hospitalización',
                        'uci' => 'UCI',
                        'ucin' => 'UCIN',
                    ])
                    ->required(),
                TextInput::make('piso')
                    ->helperText('Opcional'),
            ]);
    }
}
