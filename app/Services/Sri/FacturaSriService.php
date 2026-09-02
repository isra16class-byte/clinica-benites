<?php

namespace App\Services\Sri;

use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Envía una Factura al SRI (Ecuador) usando dazza-dev/laravel-sri-ec.
 *
 * NO PROBADO: escrito a mano contra el código fuente real del paquete
 * (clonado en el sandbox, ver docs/FACTURACION_ELECTRONICA_SRI.md), sin
 * poder instalarlo ni correrlo — sin PHP/Composer disponibles ahí. Probar
 * primero en `SRI_AMBIENTE=pruebas` (ambiente de certificación del SRI)
 * antes de cambiar a producción.
 *
 * Flujo verificado en DazzaDev\SriEc\Client::sendDocument():
 *   1. setDocumentType('invoice') + setDocumentData($array) construye el
 *      Document interno, que genera solo la clave de acceso de 49 dígitos
 *      (no hay que calcularla a mano).
 *   2. signDocument() firma el XML con el certificado .p12 configurado.
 *   3. Envía a recepción y luego a autorización del SRI; si el SRI
 *      rechaza, lanza DazzaDev\SriEc\Exceptions\DocumentException.
 *   4. Si autoriza, devuelve un array con 'status', 'authorized_document'
 *      (['access_key' => numeroAutorizacion, 'xml', 'date']), 'messages',
 *      'attempts' — el nombre 'access_key' ahí es confuso (es en realidad
 *      el número de autorización que da el SRI, distinto de la clave de
 *      acceso de 49 dígitos que ya trae el documento desde el paso 1;
 *      verificado leyendo AuthorizationClient::getAuthorizationNumber()
 *      en dazza-dev/sri-sender).
 */
class FacturaSriService
{
    public function __construct(
        private readonly FacturaSriMapper $mapper = new FacturaSriMapper,
    ) {}

    /**
     * Chequeos previos a intentar emitir — ninguno de estos requiere red
     * ni el paquete instalado, así que se puede llamar aunque
     * `dazza-dev/laravel-sri-ec` todavía no esté en el vendor/.
     *
     * @return string[] Lista de motivos por los que no se puede emitir
     *                   todavía (vacía si sí se puede).
     */
    public function motivosBloqueoEmision(Factura $factura): array
    {
        $motivos = [];

        if (! class_exists(\DazzaDev\LaravelSriEc\Facades\LaravelSriEc::class)) {
            $motivos[] = 'El paquete dazza-dev/laravel-sri-ec no está instalado todavía (correr "composer require" en el entorno real).';
        }

        if (! config('clinica.empresa.ruc') || ! config('clinica.empresa.direccion_matriz')) {
            $motivos[] = 'Faltan RUC/dirección matriz de la clínica en config/clinica.php (.env).';
        }

        if (! config('clinica.sri.certificado_path') || ! config('clinica.sri.certificado_password')) {
            $motivos[] = 'Falta el certificado digital .p12 (SRI_CERTIFICATE_PATH / SRI_CERTIFICATE_PASSWORD).';
        }

        if ($factura->lineas()->doesntExist()) {
            $motivos[] = 'La factura no tiene líneas de detalle.';
        }

        if (! $factura->forma_pago) {
            $motivos[] = 'La factura no tiene forma de pago (código SRI).';
        }

        if (in_array($factura->estado_sri, ['autorizada'], true)) {
            $motivos[] = 'La factura ya fue autorizada por el SRI — no se puede volver a emitir.';
        }

        return $motivos;
    }

    public function puedeEmitir(Factura $factura): bool
    {
        return $this->motivosBloqueoEmision($factura) === [];
    }

    /**
     * Emite la factura al SRI. Asigna el secuencial en este momento (no
     * antes, ver comentario en la migración de facturas) y guarda el
     * resultado en el registro, sea éxito o error.
     *
     * @throws RuntimeException si `motivosBloqueoEmision()` no está vacío.
     */
    public function emitir(Factura $factura): Factura
    {
        $motivos = $this->motivosBloqueoEmision($factura);

        if ($motivos !== []) {
            throw new RuntimeException('No se puede emitir la factura al SRI: '.implode(' ', $motivos));
        }

        DB::transaction(function () use ($factura): void {
            if (! $factura->secuencial) {
                $factura->forceFill([
                    'secuencial' => $this->siguienteSecuencial($factura),
                ])->save();
            }
        });

        $factura->refresh();

        $client = \DazzaDev\LaravelSriEc\Facades\LaravelSriEc::getClient();

        $client->setCertificate([
            'path' => config('clinica.sri.certificado_path'),
            'password' => config('clinica.sri.certificado_password'),
        ]);

        $client->setDocumentType('invoice');

        try {
            $client->setDocumentData($this->mapper->toArray($factura));

            $factura->forceFill([
                'clave_acceso' => $client->getAccessKey(),
                'ambiente_sri' => config('clinica.sri.ambiente'),
                'estado_sri' => 'pendiente',
            ])->save();

            $resultado = $client->sendDocument();

            $factura->forceFill([
                'estado_sri' => 'autorizada',
                'numero_autorizacion' => $resultado['authorized_document']['access_key'] ?? null,
                'fecha_autorizacion' => $resultado['authorized_document']['date'] ?? now(),
                'mensaje_sri' => null,
            ])->save();
        } catch (Throwable $excepcion) {
            $factura->forceFill([
                'estado_sri' => 'error',
                'mensaje_sri' => $excepcion->getMessage(),
            ])->save();

            throw $excepcion;
        }

        return $factura->refresh();
    }

    /**
     * Siguiente secuencial (9 dígitos, con ceros a la izquierda) para el
     * establecimiento+punto de emisión de esta factura — el máximo
     * asignado hasta ahora + 1, o '000000001' si es la primera.
     *
     * OJO: esto asume una sola instancia emitiendo a la vez. Si en algún
     * momento hay concurrencia real (dos cajas emitiendo simultáneamente),
     * esta consulta necesita un lock (`lockForUpdate()`) para no repetir
     * secuencial — no se agregó acá para no complicar código que todavía
     * no se puede probar.
     */
    private function siguienteSecuencial(Factura $factura): string
    {
        $maximo = Factura::query()
            ->where('establecimiento', $factura->establecimiento)
            ->where('punto_emision', $factura->punto_emision)
            ->whereNotNull('secuencial')
            ->max('secuencial');

        $siguiente = $maximo ? ((int) $maximo) + 1 : 1;

        return str_pad((string) $siguiente, 9, '0', STR_PAD_LEFT);
    }
}
