<?php

namespace App\Filament\Resources\MovimientosInventario\Schemas;

use App\Models\LoteInventario;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MovimientoInventarioForm
{
    /**
     * Lista fija de áreas (sección 6.3 de MEMORIA.md, punto 4 pendiente):
     * asumimos que "farmacia" no necesita ser una entidad propia con su
     * propio Resource todavía — alcanza con un valor de texto en el
     * movimiento. Editable/ampliable después si la clínica confirma que
     * necesita más estructura (horario, responsable, etc. por área).
     */
    private const AREAS = [
        'farmacia' => 'Farmacia',
        'quirofano' => 'Quirófano',
        'admision' => 'Admisión',
        'facturacion' => 'Facturación',
        'bodega' => 'Bodega',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lote_id')
                    ->relationship('lote', 'numero_lote')
                    ->label('Lote')
                    ->getOptionLabelFromRecordUsing(
                        fn (LoteInventario $record): string => "{$record->item->nombre} — Lote {$record->numero_lote} (vence {$record->fecha_vencimiento->format('d/m/Y')})"
                    )
                    ->searchable(['numero_lote'])
                    ->preload()
                    ->required(),
                Select::make('tipo_movimiento')
                    ->label('Tipo de movimiento')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'traslado' => 'Traslado',
                        'ajuste' => 'Ajuste',
                    ])
                    ->required()
                    ->live(),
                TextInput::make('cantidad')
                    ->numeric()
                    ->required(),
                Select::make('area_origen')
                    ->label('Área de origen')
                    ->options(self::AREAS)
                    ->visible(fn ($get): bool => in_array($get('tipo_movimiento'), ['salida', 'traslado'])),
                Select::make('area_destino')
                    ->label('Área de destino')
                    ->options(self::AREAS)
                    ->visible(fn ($get): bool => in_array($get('tipo_movimiento'), ['entrada', 'traslado'])),
                DateTimePicker::make('fecha_hora')
                    ->label('Fecha y hora')
                    ->default(now())
                    ->required(),
                Select::make('paciente_id')
                    ->relationship('paciente', 'nombres')
                    ->label('Paciente (si es consumo en atención)')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get): bool => $get('tipo_movimiento') === 'salida'),
                Select::make('cita_id')
                    ->relationship('cita', 'id')
                    ->label('Cita relacionada')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get): bool => $get('tipo_movimiento') === 'salida'),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }
}
