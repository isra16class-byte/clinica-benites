<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineaFactura extends Model
{
    protected $table = 'lineas_factura';

    protected $fillable = [
        'factura_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento',
        'codigo_iva',
        'subtotal',
    ];

    /**
     * Catálogo de tarifas de IVA del SRI (código => [porcentaje, etiqueta]),
     * verificado contra dazza-dev/sri-xml-generator (`src/Data/taxes/2.json`
     * — tax_type 2 = IVA). El porcentaje es el que se usa para calcular el
     * IVA de la línea; '6' y '7' no llevan porcentaje (no gravan IVA).
     */
    public const TARIFAS_IVA = [
        '0' => ['porcentaje' => 0.0, 'label' => '0%'],
        '4' => ['porcentaje' => 0.15, 'label' => '15%'],
        '6' => ['porcentaje' => 0.0, 'label' => 'No objeto de impuesto'],
        '7' => ['porcentaje' => 0.0, 'label' => 'Exento de IVA'],
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Porcentaje de IVA de esta línea (0.0 a 1.0), según su codigo_iva.
     */
    public function porcentajeIva(): float
    {
        return self::TARIFAS_IVA[$this->codigo_iva]['porcentaje'] ?? 0.0;
    }

    public function ivaCalculado(): float
    {
        return round($this->subtotal * $this->porcentajeIva(), 2);
    }

    /**
     * Recalcula `subtotal` a partir de cantidad/precio_unitario/descuento.
     * Se llama antes de guardar (ver boot()) para que la columna quede
     * siempre consistente con los datos que la originan.
     */
    public function calcularSubtotal(): float
    {
        return round(((float) $this->cantidad * (float) $this->precio_unitario) - (float) $this->descuento, 2);
    }

    protected static function booted(): void
    {
        static::saving(function (LineaFactura $linea): void {
            $linea->subtotal = $linea->calcularSubtotal();
        });

        // Cada vez que una línea se crea/edita/elimina, la factura dueña
        // recalcula sus totales (subtotal/iva/total) — así el header nunca
        // queda desincronizado del detalle sin tener que recalcular a mano
        // desde el Form. Ver Factura::recalcularTotales().
        static::saved(function (LineaFactura $linea): void {
            $linea->factura?->recalcularTotales();
        });

        static::deleted(function (LineaFactura $linea): void {
            $linea->factura?->recalcularTotales();
        });
    }
}
