<?php

namespace App\Filament\Resources\Facturas\Schemas;

use App\Models\Factura;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class FacturaForm
{
    /**
     * Facturación electrónica SRI (MEMORIA.md sección 6): `monto` (número
     * suelto) desaparece de este formulario — el total ahora se calcula
     * automáticamente desde el detalle (tab "Líneas" dentro de Editar
     * Factura, ver LineasFacturaRelationManager) apenas se guarda al menos
     * una línea. `metodo_pago` (texto libre) se reemplaza por `forma_pago`,
     * con los códigos fijos del catálogo oficial del SRI.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->nombres} {$record->apellidos}")
                    ->label('Paciente')
                    ->searchable(['nombres', 'apellidos', 'cedula'])
                    ->preload()
                    ->required(),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->searchable()
                    ->preload(),
                Select::make('estado_pago')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'anulado' => 'Anulado',
                    ])
                    ->required()
                    ->default('pendiente'),
                Select::make('forma_pago')
                    ->label('Forma de pago')
                    ->options(Factura::FORMAS_PAGO)
                    ->native(false)
                    ->helperText('Catálogo oficial del SRI (Tabla 24) — obligatorio si la factura se va a emitir electrónicamente.'),
                DatePicker::make('fecha')
                    ->required()
                    ->default(now()),
                Placeholder::make('lineas_ayuda')
                    ->label('Detalle (líneas)')
                    ->content('El total se agrega en la pestaña "Líneas" una vez creada la factura — el SRI exige detalle por ítem, no un monto suelto.')
                    ->visibleOn('create'),
            ]);
    }
}
