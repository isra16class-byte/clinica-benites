<?php

namespace App\Services\Sri;

use App\Models\Factura;
use App\Models\LineaFactura;
use App\Models\Paciente;
use InvalidArgumentException;

/**
 * Arma el array `$documentData` que espera
 * `DazzaDev\LaravelSriEc\Facades\LaravelSriEc::getClient()->setDocumentData()`.
 *
 * Estructura verificada leyendo el código fuente real de
 * dazza-dev/sri-xml-generator (clonado en el sandbox, no instalado como
 * dependencia — ver docs/FACTURACION_ELECTRONICA_SRI.md) contra:
 * `Document::initialize()`, `Models/Company.php`, `Models/Customer.php`,
 * `Models/Establishment.php`/`EmissionPoint.php` (ambos extienden
 * `BaseModel`, que exige `code`+`name`), `Models/Document/LineItem.php`,
 * `Models/Tax/Tax.php`, `Models/Payment/Payment.php`,
 * `Models/Totals/Totals.php`.
 *
 * NO PROBADO contra el paquete real (sin PHP/Composer en el sandbox donde
 * se escribió esto) — revisar con cuidado al integrar en el entorno real,
 * en particular el mapeo de `codigo_iva` a `taxes[].percentage_code`.
 */
class FacturaSriMapper
{
    /**
     * Unidad de medida fija para todas las líneas: el proyecto no tiene
     * todavía un concepto de "unidad de medida" por servicio/producto
     * (ver lineas_factura, que es texto libre). 'UNI' (unidad) es un
     * default razonable para servicios de salud facturados por sesión o
     * ítem — revisar si en algún momento se facturan insumos por
     * kilo/litro/etc., que sí necesitarían otra unidad.
     */
    private const UNIDAD_MEDIDA_DEFAULT = 'UNI';

    public function toArray(Factura $factura): array
    {
        $factura->loadMissing(['lineas', 'paciente']);

        if (! $factura->paciente instanceof Paciente) {
            throw new InvalidArgumentException("La factura #{$factura->id} no tiene un paciente asociado — no se puede armar el documento SRI.");
        }

        if ($factura->lineas->isEmpty()) {
            throw new InvalidArgumentException("La factura #{$factura->id} no tiene líneas — el SRI exige al menos un ítem de detalle.");
        }

        if (! $factura->forma_pago) {
            throw new InvalidArgumentException("La factura #{$factura->id} no tiene forma de pago (código SRI) — obligatoria para emitir.");
        }

        return [
            'sequential' => $factura->secuencial,
            'date' => optional($factura->fecha)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'establishment' => [
                'code' => $factura->establecimiento,
                'name' => config('clinica.establecimiento.name'),
                'address' => config('clinica.empresa.direccion_matriz'),
            ],
            'emission_point' => [
                'code' => $factura->punto_emision,
                'name' => config('clinica.punto_emision.name'),
            ],
            'company' => $this->mapCompany(),
            'customer' => $this->mapCustomer($factura->paciente),
            'line_items' => $factura->lineas
                ->map(fn (LineaFactura $linea, int $indice): array => $this->mapLineaFactura($linea, $indice))
                ->values()
                ->all(),
            'payments' => [
                [
                    'payment_method' => $factura->forma_pago,
                    'amount' => (float) $factura->total,
                ],
            ],
            'totals' => $this->mapTotals($factura),
        ];
    }

    private function mapCompany(): array
    {
        $ruc = config('clinica.empresa.ruc');
        $direccion = config('clinica.empresa.direccion_matriz');

        if (! $ruc || ! $direccion) {
            // Bloqueo real documentado en MEMORIA.md sección 6: el cliente
            // todavía no tiene RUC/dirección matriz confirmados. Falla acá,
            // explícito, en vez de mandar un XML con datos placeholder al
            // SRI (que lo rechazaría o, peor, lo aceptaría con datos
            // fiscales incorrectos).
            throw new InvalidArgumentException(
                'Faltan datos tributarios de la clínica en config/clinica.php (CLINICA_RUC / CLINICA_DIRECCION_MATRIZ) — no se puede armar el documento SRI todavía.'
            );
        }

        return [
            'ruc' => $ruc,
            'legal_name' => config('clinica.empresa.razon_social'),
            'trade_name' => config('clinica.empresa.nombre_comercial'),
            'head_office_address' => $direccion,
            'special_taxpayer_number' => config('clinica.empresa.contribuyente_especial'),
            'withholding_agent' => (bool) config('clinica.empresa.agente_retencion'),
            'requires_accounting' => (bool) config('clinica.empresa.obligado_contabilidad'),
        ];
    }

    private function mapCustomer(Paciente $paciente): array
    {
        $tipo = Paciente::TIPOS_IDENTIFICACION[$paciente->tipo_identificacion] ?? null;

        if (! $tipo) {
            throw new InvalidArgumentException("El paciente #{$paciente->id} tiene un tipo_identificacion desconocido: {$paciente->tipo_identificacion}.");
        }

        return [
            'identification_type' => $tipo['sri'],
            'identification_number' => $paciente->cedula,
            'name' => trim("{$paciente->nombres} {$paciente->apellidos}"),
            'address' => $paciente->direccion,
        ];
    }

    private function mapLineaFactura(LineaFactura $linea, int $indice): array
    {
        return [
            // El proyecto no tiene un catálogo de servicios con código
            // propio (ver nota en la migración de lineas_factura) — se usa
            // el id de la línea con padding como código interno, único y
            // estable, hasta que exista un catálogo real.
            'code' => 'SRV-'.str_pad((string) $linea->id, 6, '0', STR_PAD_LEFT),
            'description' => $linea->descripcion,
            'unit' => self::UNIDAD_MEDIDA_DEFAULT,
            'quantity' => (float) $linea->cantidad,
            'unit_price' => (float) $linea->precio_unitario,
            'discount' => (float) $linea->descuento,
            'total_price_without_tax' => (float) $linea->subtotal,
            'taxes' => [
                [
                    'code' => 2, // Tipo de impuesto 2 = IVA (tax-types.json)
                    'percentage_code' => $linea->codigo_iva,
                    'taxable_base' => (float) $linea->subtotal,
                    'value' => $linea->ivaCalculado(),
                ],
            ],
        ];
    }

    private function mapTotals(Factura $factura): array
    {
        // Impuestos agregados: agrupa las líneas por codigo_iva y suma su
        // base imponible y valor — el SRI exige un renglón de impuesto por
        // cada tarifa distinta usada en el detalle, no una sola línea con
        // el total general.
        $impuestosPorTarifa = $factura->lineas
            ->groupBy('codigo_iva')
            ->map(function ($lineas, string $codigoIva): array {
                return [
                    'code' => 2,
                    'percentage_code' => $codigoIva,
                    'taxable_base' => round((float) $lineas->sum('subtotal'), 2),
                    'value' => round($lineas->sum(fn (LineaFactura $linea): float => $linea->ivaCalculado()), 2),
                ];
            })
            ->values()
            ->all();

        return [
            'subtotal' => (float) $factura->subtotal,
            'total_discount' => (float) $factura->lineas->sum('descuento'),
            'taxes' => $impuestosPorTarifa,
            'total' => (float) $factura->total,
        ];
    }
}
