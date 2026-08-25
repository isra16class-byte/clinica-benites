<?php

namespace App\Filament\Resources\Cirugias\Pages;

use App\Filament\Resources\Cirugias\CirugiaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCirugia extends CreateRecord
{
    protected static string $resource = CirugiaResource::class;

    /**
     * Datos del Repeater "medicosAdicionales" — no es un campo de `cirugias`
     * (Cirugia::$fillable no lo incluye), así que se guarda aparte y se
     * sincroniza con la tabla pivote `cirugia_medico` en afterCreate().
     * No se usa Repeater::relationship() porque eso asume crear un
     * `Medico` nuevo por cada fila (ver bug reportado: intentaba insertar
     * un médico vacío en vez de asociar uno existente con su rol).
     */
    protected array $medicosAdicionales = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->medicosAdicionales = $data['medicosAdicionales'] ?? [];
        unset($data['medicosAdicionales']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->medicosAdicionales()->sync(
            collect($this->medicosAdicionales)
                ->filter(fn (array $item): bool => filled($item['medico_id'] ?? null))
                ->mapWithKeys(fn (array $item): array => [
                    $item['medico_id'] => ['rol' => $item['rol'] ?? null],
                ])
                ->all()
        );
    }
}
