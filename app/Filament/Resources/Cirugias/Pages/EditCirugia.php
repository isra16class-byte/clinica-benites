<?php

namespace App\Filament\Resources\Cirugias\Pages;

use App\Filament\Concerns\HasBackFormAction;
use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Models\Cirugia;
use App\Models\Medico;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCirugia extends EditRecord
{
    use HasBackFormAction;

    protected static string $resource = CirugiaResource::class;

    /**
     * Ver nota en CreateCirugia: "medicosAdicionales" no es un campo de
     * `cirugias`, se maneja aparte contra la tabla pivote `cirugia_medico`.
     */
    protected array $medicosAdicionales = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Cirugia $record): bool => CirugiaResource::canDelete($record)),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['medicosAdicionales'] = $this->record->medicosAdicionales
            ->map(fn (Medico $medico): array => [
                'medico_id' => $medico->id,
                'rol' => $medico->pivot->rol,
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->medicosAdicionales = $data['medicosAdicionales'] ?? [];
        unset($data['medicosAdicionales']);

        return $data;
    }

    protected function afterSave(): void
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
