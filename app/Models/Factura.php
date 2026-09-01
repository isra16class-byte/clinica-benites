<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $fillable = [
        'paciente_id',
        'cita_id',
        'total',
        'subtotal',
        'iva',
        'estado_pago',
        'forma_pago',
        'estado_sri',
        'ambiente_sri',
        'establecimiento',
        'punto_emision',
        'secuencial',
        'clave_acceso',
        'numero_autorizacion',
        'mensaje_sri',
        'fecha_autorizacion',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'fecha_autorizacion' => 'datetime',
        ];
    }

    /**
     * Formas de pago del catálogo oficial del SRI (Tabla 24), verificado
     * contra dazza-dev/sri-xml-generator (`src/Data/payment-methods.json`).
     * Se guarda el código tal cual lo pide el SRI (clave del array) para no
     * necesitar traducción al momento de emitir electrónicamente.
     */
    public const FORMAS_PAGO = [
        '01' => 'Efectivo (sin utilización del sistema financiero)',
        '15' => 'Compensación de deudas',
        '16' => 'Tarjeta de débito',
        '17' => 'Dinero electrónico',
        '18' => 'Tarjeta prepago',
        '19' => 'Tarjeta de crédito',
        '20' => 'Otros con utilización del sistema financiero',
        '21' => 'Endoso de títulos',
    ];

    public const ESTADOS_SRI = [
        'no_emitida' => 'No emitida',
        'pendiente' => 'Pendiente',
        'autorizada' => 'Autorizada',
        'rechazada' => 'Rechazada',
        'error' => 'Error',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(LineaFactura::class);
    }

    /**
     * Número de comprobante en el formato del SRI (001-001-000000001).
     * Null mientras no tenga `secuencial` asignado (factura sin emitir).
     */
    public function numeroComprobante(): ?string
    {
        if (! $this->secuencial) {
            return null;
        }

        return "{$this->establecimiento}-{$this->punto_emision}-{$this->secuencial}";
    }

    /**
     * Recalcula subtotal/iva/total desde las líneas actuales y guarda el
     * resultado en la factura, sin disparar de nuevo los observers de
     * LineaFactura (evita recursión — ver LineaFactura::booted()).
     */
    public function recalcularTotales(): void
    {
        $lineas = $this->lineas()->get();

        $subtotal = $lineas->sum(fn (LineaFactura $linea): float => (float) $linea->subtotal);
        $iva = $lineas->sum(fn (LineaFactura $linea): float => $linea->ivaCalculado());

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'iva' => round($iva, 2),
            'total' => round($subtotal + $iva, 2),
        ])->saveQuietly();
    }
}
