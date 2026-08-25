<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cama extends Model
{
    protected $fillable = [
        'numero',
        'tipo',
        'piso',
    ];

    public function internamientos(): HasMany
    {
        return $this->hasMany(Internamiento::class);
    }

    /**
     * Ocupada = tiene un internamiento activo (sin fecha_alta). No es una
     * columna guardada — se deriva siempre en vivo, mismo criterio que el
     * stock del módulo de inventario (sección 6.3 de MEMORIA.md), para que
     * nunca se desincronice de la realidad.
     */
    public function ocupada(): bool
    {
        return $this->internamientos()->whereNull('fecha_alta')->exists();
    }

    public function internamientoActivo(): ?Internamiento
    {
        return $this->internamientos()->whereNull('fecha_alta')->first();
    }
}
