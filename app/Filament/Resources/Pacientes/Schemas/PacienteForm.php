<?php

namespace App\Filament\Resources\Pacientes\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PacienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombres')
                    ->required(),
                TextInput::make('apellidos')
                    ->required(),
                TextInput::make('cedula')
                    ->label('Cédula / RUC')
                    ->required()
                    ->unique(table: 'pacientes', column: 'cedula', ignoreRecord: true),
                // Facturación electrónica SRI (MEMORIA.md sección 6): el
                // comprobante exige declarar el tipo de identificación con
                // uno de los códigos fijos del SRI. Se guarda por separado
                // del número (columna `cedula`, sin renombrar — ver
                // migración) para no romper todo lo que ya depende de ese
                // nombre de columna en el resto del proyecto.
                Select::make('tipo_identificacion')
                    ->label('Tipo de identificación')
                    ->options(array_map(fn (array $tipo): string => $tipo['label'], Paciente::TIPOS_IDENTIFICACION))
                    ->native(false)
                    ->required()
                    ->default('cedula'),
                DatePicker::make('fecha_nacimiento'),
                TextInput::make('telefono')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('direccion'),
                TextInput::make('sexo'),
                // Módulo "Antecedentes" del expediente clínico (MEMORIA.md
                // sección 8): dato único del paciente, no una lista de
                // entradas, por eso vive acá y no en el tab de Antecedentes.
                Select::make('grupo_sanguineo')
                    ->label('Grupo sanguíneo')
                    ->options([
                        'O+' => 'O+', 'O-' => 'O-',
                        'A+' => 'A+', 'A-' => 'A-',
                        'B+' => 'B+', 'B-' => 'B-',
                        'AB+' => 'AB+', 'AB-' => 'AB-',
                    ])
                    ->native(false)
                    ->helperText('Dejar vacío si no está confirmado todavía.'),
            ]);
    }
}
