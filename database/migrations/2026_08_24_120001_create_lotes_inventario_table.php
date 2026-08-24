<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Trazabilidad por lote y fecha de vencimiento (sección 6.3 de
     * MEMORIA.md) — no es opcional en la práctica real de farmacia
     * hospitalaria (norma sanitaria + seguridad del paciente), estándar
     * FEFO (First-Expired-First-Out). Dos cajas del mismo ítem con
     * vencimientos distintos son unidades separadas.
     *
     * No tiene columna `cantidad`: igual que `items_inventario`, el stock de
     * un lote se deriva de la suma de sus `movimientos_inventario`.
     */
    public function up(): void
    {
        Schema::create('lotes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items_inventario');
            $table->string('numero_lote');
            $table->date('fecha_vencimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes_inventario');
    }
};
