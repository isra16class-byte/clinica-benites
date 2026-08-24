<?php

namespace App\Filament\Resources\MovimientosInventario\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\MovimientosInventario\MovimientoInventarioResource;
use App\Models\MovimientoInventario;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMovimientoInventario extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = MovimientoInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (MovimientoInventario $record): bool => MovimientoInventarioResource::canDelete($record)),
        ];
    }
}
