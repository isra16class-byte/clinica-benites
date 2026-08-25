<?php

namespace App\Filament\Resources\Quirofanos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuirofanoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero')
                    ->required()
                    ->unique(table: 'quirofanos', column: 'numero', ignoreRecord: true),
                TextInput::make('nombre')
                    ->helperText('Opcional'),
                Select::make('estado')
                    ->options([
                        'libre' => 'Libre',
                        'preparacion' => 'En preparación',
                        'en_cirugia' => 'En cirugía',
                        'limpieza' => 'En limpieza',
                    ])
                    ->required()
                    ->default('libre'),
            ]);
    }
}
