<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemInventario extends Model
{
    protected $table = 'items_inventario';

    protected $fillable = [
        'nombre',
        'tipo',
        'unidad_medida',
        'stock_minimo',
        'precio_unitario',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(LoteInventario::class, 'item_id');
    }

    /**
     * Stock actual del ítem = suma del stock actual de todos sus lotes.
     * No es una columna guardada — se recalcula siempre desde los
     * movimientos, para que nunca se desincronice de la realidad (mismo
     * criterio que el estado de una cama, sección 6.2 de MEMORIA.md).
     */
    public function stockActual(): float
    {
        return $this->lotes->sum(fn (LoteInventario $lote): float => $lote->stockActual());
    }

    public function bajoStockMinimo(): bool
    {
        if ($this->stock_minimo === null) {
            return false;
        }

        return $this->stockActual() < $this->stock_minimo;
    }
}
