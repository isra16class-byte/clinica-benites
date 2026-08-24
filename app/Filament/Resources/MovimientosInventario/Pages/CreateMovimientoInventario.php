<?php

namespace App\Filament\Resources\MovimientosInventario\Pages;

use App\Filament\Resources\MovimientosInventario\MovimientoInventarioResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMovimientoInventario extends CreateRecord
{
    protected static string $resource = MovimientoInventarioResource::class;

    /**
     * `user_id` no es un campo del formulario — se registra automáticamente
     * como quien está logueado, para no depender de que lo seleccione a
     * mano (y para que no se pueda registrar un movimiento a nombre de otra
     * persona por error).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
