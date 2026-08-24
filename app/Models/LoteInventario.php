<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteInventario extends Model
{
    protected $table = 'lotes_inventario';

    protected $fillable = [
        'item_id',
        'numero_lote',
        'fecha_vencimiento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemInventario::class, 'item_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'lote_id');
    }

    /**
     * Stock actual del lote, derivado de sus movimientos (nunca editado a
     * mano): entrada y ajuste suman, salida resta. "Traslado" no cambia la
     * cantidad total — solo registra a qué área se movió, ya que este
     * diseño no modela stock por área todavía (ver decisión pendiente en
     * sección 6.3 de MEMORIA.md sobre si "farmacia" necesita ser su propia
     * entidad).
     */
    public function stockActual(): float
    {
        $suma = $this->movimientos()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN tipo_movimiento IN ('entrada', 'ajuste') THEN cantidad ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN tipo_movimiento = 'salida' THEN cantidad ELSE 0 END), 0)
                as stock
            ")
            ->value('stock');

        return (float) $suma;
    }

    public function vencido(): bool
    {
        return $this->fecha_vencimiento->isPast();
    }

    public function porVencer(int $dias = 90): bool
    {
        return ! $this->vencido() && $this->fecha_vencimiento->lte(now()->addDays($dias));
    }
}
