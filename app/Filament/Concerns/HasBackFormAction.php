<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

/**
 * Usado en las páginas de Editar de cada Resource para reemplazar el botón
 * "Cancelar" (que en Filament, por defecto, hace lo mismo que ya hace el
 * breadcrumb) por un botón "Atrás" que lleva directo al listado del recurso.
 *
 * No se usa en las páginas de Crear: ahí "Cancelar" sí es útil (descarta un
 * formulario que todavía no se guardó), a diferencia de Editar, donde los
 * cambios ya guardados no se "cancelan" con ese botón.
 */
trait HasBackFormAction
{
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getBackFormAction(),
        ];
    }

    protected function getBackFormAction(): Action
    {
        return Action::make('back')
            ->label('Atrás')
            ->icon(Heroicon::OutlinedArrowLeft)
            ->color('gray')
            ->url(fn (): string => $this->getResourceUrl());
    }
}
