<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix: `total` (renombrada de `monto` en
     * 2026_09_01_130001_add_facturacion_electronica_a_facturas_table) se
     * quedó como `decimal(10,2)` NOT NULL sin default — a diferencia de
     * `subtotal`/`iva`, que sí recibieron `->default(0)` en esa misma
     * migración. El diseño de facturación SRI crea la Factura ANTES de
     * tener líneas (el total se calcula recién cuando se guarda la primera
     * línea, ver LineaFactura::booted()/Factura::recalcularTotales()) —
     * tanto en el seeder de demo como en el formulario real de Filament
     * (FacturaForm.php, tab "Líneas" solo existe en Editar, no en Crear).
     * Sin default, ese insert inicial siempre falla con
     * "Field 'total' doesn't have a default value" (SQLSTATE 1364),
     * reproducido al correr `db:seed --class=DemoHistoricoSeeder`.
     *
     * Se usa SQL crudo (no `->change()`) porque el proyecto no tiene
     * `doctrine/dbal` instalado.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE facturas MODIFY total DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE facturas MODIFY total DECIMAL(10,2) NOT NULL');
    }
};
