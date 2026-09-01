<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Detalle de factura (MEMORIA.md sección 6): el SRI exige que cada
     * comprobante tenga al menos una línea con descripción, cantidad,
     * precio unitario, descuento y tarifa de IVA — no un monto suelto como
     * tenía `facturas` hasta ahora. No se vincula a `items_inventario`: una
     * factura de la clínica cobra tanto insumos/medicamentos (si en algún
     * momento se facturan por separado) como servicios que no son ítems de
     * inventario (consulta, cirugía, día de internamiento) — se deja como
     * texto libre + precio, igual de flexible que el resto del proyecto
     * hasta que exista un catálogo de servicios facturables propio.
     *
     * `codigo_iva`: código fijo del catálogo de tarifas de IVA del SRI
     * (verificado contra dazza-dev/sri-xml-generator,
     * `src/Data/taxes/2.json` — tax_type 2 = IVA). Default '0' (tarifa 0%):
     * la LRTI (Ley de Régimen Tributario Interno, art. 55-56) grava con
     * tarifa 0% los servicios de salud y medicinas — la mayoría de lo que
     * factura una clínica cae ahí, a diferencia del 15% general. **No
     * confirmado con un contador**: queda como default razonable, cada
     * línea permite cambiarlo si corresponde (ver nota en MEMORIA.md).
     */
    public function up(): void
    {
        Schema::create('lineas_factura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            // '0' = 0% | '4' = 15% | '6' = No objeto de impuesto | '7' = Exento de IVA
            $table->string('codigo_iva', 2)->default('0');
            // subtotal = (cantidad * precio_unitario) - descuento, sin IVA.
            // Se guarda calculado (no solo derivado en PHP) para que quede
            // fijo en el detalle de la factura aunque cambie el precio del
            // ítem de referencia más adelante — mismo criterio que un
            // comprobante real, que no se recalcula solo.
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineas_factura');
    }
};
