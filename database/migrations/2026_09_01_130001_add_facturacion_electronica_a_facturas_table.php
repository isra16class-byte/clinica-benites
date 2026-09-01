<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Facturación electrónica SRI (MEMORIA.md sección 6, confirmado por el
     * cliente el 01 sep 2026): el SRI exige que una factura tenga detalle
     * por línea (ver migración de `lineas_factura`), no un monto suelto, y
     * un número de comprobante con establecimiento + punto de emisión +
     * secuencial (los asigna el SRI al registrar el punto de venta — el
     * cliente todavía no los tiene, ver sección 6). Esta migración prepara
     * la tabla `facturas` para ambos casos: mientras el trámite del SRI no
     * esté listo, el sistema sigue funcionando como comprobante interno
     * (estado_sri = 'no_emitida'); cuando el cliente tenga RUC/
     * establecimiento/punto de emisión/certificado .p12, alcanza con
     * cargarlos (ver config/clinica.php y .env) sin tocar esta estructura.
     */
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // `monto` pasa a ser el total final (mismo significado que
            // tenía, solo se renombra para no chocar con `subtotal`/`iva`
            // nuevos). El monto real ahora se calcula desde `lineas_factura`
            // — ver LineaFactura::recalcularTotalesFactura().
            $table->renameColumn('monto', 'total');
        });

        Schema::table('facturas', function (Blueprint $table) {
            // Descuentos van por línea (`lineas_factura.descuento`), no acá
            // — subtotal/iva/total de la factura son la suma de sus líneas,
            // ver Factura::recalcularTotales().
            $table->decimal('subtotal', 10, 2)->default(0)->after('total');
            $table->decimal('iva', 10, 2)->default(0)->after('subtotal');

            // Reemplaza `metodo_pago` (texto libre) por un código fijo del
            // catálogo oficial del SRI (Tabla 24 — Formas de pago), igual
            // que se hizo con tipo_identificacion en pacientes. Verificado
            // contra dazza-dev/sri-xml-generator (`payment-methods.json`).
            $table->string('forma_pago')->nullable()->after('metodo_pago');

            // --- Estado de emisión electrónica ante el SRI ---
            // 'no_emitida'  : comprobante interno, todavía no se envía al SRI
            //                 (estado por defecto — es el único disponible
            //                 mientras falten RUC/establecimiento/punto de
            //                 emisión/certificado .p12, ver sección 6).
            // 'pendiente'   : se intentó enviar, sin respuesta aún.
            // 'autorizada'  : el SRI la autorizó — comprobante válido.
            // 'rechazada'   : el SRI la rechazó (ver mensaje_sri).
            // 'error'       : falló el envío antes de llegar al SRI
            //                 (certificado, conexión, datos inválidos).
            $table->string('estado_sri')->default('no_emitida')->after('forma_pago');

            // Ambiente en el que se emitió: 'pruebas' o 'produccion' (SRI
            // exige mantenerlos separados — un comprobante de pruebas nunca
            // es válido fiscalmente). Null mientras no se haya emitido.
            $table->string('ambiente_sri')->nullable()->after('estado_sri');

            $table->string('establecimiento', 3)->default('001')->after('ambiente_sri');
            $table->string('punto_emision', 3)->default('001')->after('establecimiento');
            // Secuencial de 9 dígitos (con ceros a la izquierda), único por
            // establecimiento+punto de emisión — se asigna recién al emitir,
            // no al crear la factura (evita huecos en la numeración si se
            // crea una factura y después se anula sin llegar a emitir).
            $table->string('secuencial', 9)->nullable()->after('punto_emision');

            $table->string('clave_acceso', 49)->nullable()->after('secuencial');
            $table->string('numero_autorizacion')->nullable()->after('clave_acceso');
            $table->text('mensaje_sri')->nullable()->after('numero_autorizacion');
            $table->timestamp('fecha_autorizacion')->nullable()->after('mensaje_sri');
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('metodo_pago')->nullable();
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'iva',
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
            ]);
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->renameColumn('total', 'monto');
        });
    }
};
