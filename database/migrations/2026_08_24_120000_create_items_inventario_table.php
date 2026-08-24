<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Catálogo de medicamentos e insumos (sección 6.3 de MEMORIA.md). Un solo
     * catálogo para ambos tipos (medicamento/insumo) en vez de dos tablas
     * separadas, ya que comparten los mismos campos y solo cambia `tipo`.
     *
     * A propósito NO tiene una columna `stock_actual`: el stock se deriva de
     * la suma de movimientos de cada lote (ver `movimientos_inventario` y
     * `LoteInventario::stockActual()`), igual que el estado de una cama se
     * deriva de si tiene un internamiento activo (sección 6.2) — se evita así
     * que el stock guardado se desincronice de la realidad.
     */
    public function up(): void
    {
        Schema::create('items_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo'); // medicamento | insumo
            $table->string('unidad_medida'); // ej. unidad, caja, ml, mg, frasco
            $table->integer('stock_minimo')->nullable(); // para alertas, opcional
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_inventario');
    }
};
