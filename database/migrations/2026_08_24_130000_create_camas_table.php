<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Camas para Hospitalización, UCI y UCIN (sección 6.2 de MEMORIA.md,
     * grupo 1). Las tres son variantes del mismo concepto — un paciente
     * ocupa una cama durante un rango de fechas — así que comparten esta
     * misma tabla y solo cambia `tipo`.
     *
     * A propósito NO tiene una columna `estado` (libre/ocupada): se deriva
     * de si la cama tiene un internamiento activo (sin `fecha_alta`), igual
     * criterio que el stock derivado del módulo de inventario (sección
     * 6.3) — evita que el estado guardado se desincronice de la realidad.
     */
    public function up(): void
    {
        Schema::create('camas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('tipo'); // hospitalizacion | uci | ucin
            $table->string('piso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camas');
    }
};
