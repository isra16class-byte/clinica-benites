<?php

/**
 * Datos tributarios de Clínica Benites para facturación electrónica SRI
 * (MEMORIA.md sección 6, docs/FACTURACION_ELECTRONICA_SRI.md).
 *
 * TODOS los valores son placeholders hasta que el cliente confirme:
 * - RUC, razón social, nombre comercial, dirección matriz.
 * - Código de establecimiento y punto de emisión (los asigna el SRI al
 *   registrar el punto de venta).
 * - Certificado digital .p12 (lo emite una entidad certificadora
 *   autorizada — Security Data, BCE, Uanataca, etc.).
 *
 * No cargar acá ningún dato real todavía: mientras `sri.ambiente` no se
 * cambie a 'produccion' Y exista un certificado .p12 válido, el sistema no
 * debe intentar emitir nada real al SRI (ver
 * App\Services\Sri\FacturaSriService::puedeEmitir()).
 */
return [

    'empresa' => [
        'ruc' => env('CLINICA_RUC'),
        'razon_social' => env('CLINICA_RAZON_SOCIAL', 'Clínica Benites'),
        'nombre_comercial' => env('CLINICA_NOMBRE_COMERCIAL', 'Clínica Benites'),
        'direccion_matriz' => env('CLINICA_DIRECCION_MATRIZ'),
        // Contribuyente especial / obligado a llevar contabilidad / agente
        // de retención: preguntas pendientes de confirmar con el cliente
        // (no necesariamente aplican a una clínica pequeña, pero cambian el
        // XML si sí aplican) — por defecto en false/null hasta confirmar.
        'contribuyente_especial' => env('CLINICA_CONTRIBUYENTE_ESPECIAL'),
        'obligado_contabilidad' => env('CLINICA_OBLIGADO_CONTABILIDAD', false),
        'agente_retencion' => env('CLINICA_AGENTE_RETENCION', false),
    ],

    // Establecimiento y punto de emisión por defecto — los que trae el
    // modelo Factura (`establecimiento`/`punto_emision`) son por-factura y
    // toman estos como default; se pueden usar distintos si la clínica
    // llega a tener más de un local facturando.
    'establecimiento' => [
        'code' => env('CLINICA_ESTABLECIMIENTO_CODE', '001'),
        'name' => env('CLINICA_ESTABLECIMIENTO_NAME', 'Matriz'),
    ],

    'punto_emision' => [
        'code' => env('CLINICA_PUNTO_EMISION_CODE', '001'),
        'name' => env('CLINICA_PUNTO_EMISION_NAME', 'Caja principal'),
    ],

    'sri' => [
        // 'pruebas' o 'produccion'. Arrancar SIEMPRE en 'pruebas' hasta
        // validar el flujo completo con el ambiente de certificación del
        // SRI — un comprobante autorizado en 'produccion' es fiscalmente
        // real y no se puede simplemente borrar si algo salió mal.
        'ambiente' => env('SRI_AMBIENTE', 'pruebas'),
        'certificado_path' => env('SRI_CERTIFICATE_PATH'),
        'certificado_password' => env('SRI_CERTIFICATE_PASSWORD'),
    ],

];
