<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Servicios de ambulancia (sección 6.2 de MEMORIA.md, grupo 5) —
     * servicio de transporte, no de permanencia, por eso es la tabla más
     * simple del módulo. `paciente_id` nullable a propósito: un traslado
     * puede involucrar a alguien que todavía no es paciente registrado en
     * el sistema (ej. una emergencia externa), igual criterio que
     * `MovimientoInventario.paciente_id` en la sección 6.3.
     */
    public function up(): void
    {
        Schema::create('servicios_ambulancia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes');
            $table->string('origen');
            $table->string('destino');
            $table->dateTime('fecha_hora');
            $table->string('motivo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_ambulancia');
    }
};
