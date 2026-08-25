<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Internamientos (sección 6.2 de MEMORIA.md, grupo 1) — un paciente
     * ocupando una cama durante un rango de fechas. `fecha_alta` nullable:
     * nulo mientras sigue internado, es lo que usa `Cama::ocupada()` para
     * derivar el estado de la cama sin guardarlo aparte.
     *
     * `origen`/`prioridad` cubren el grupo 4 (Emergencias) de la misma
     * sección: en vez de una tabla propia para "emergencias", se trata como
     * una forma de llegar a un internamiento (o a una cita, ver la
     * migración que agrega estas mismas columnas a `citas`). `prioridad`
     * usa el estándar ESI (Emergency Severity Index, 5 niveles) validado
     * con investigación externa — solo aplica cuando `origen` es
     * 'emergencia', queda nulo en internamientos programados.
     */
    public function up(): void
    {
        Schema::create('internamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('cama_id')->constrained('camas');
            $table->foreignId('medico_id')->constrained('medicos');
            $table->foreignId('cita_id')->nullable()->constrained('citas');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_alta')->nullable();
            $table->text('motivo')->nullable();
            $table->string('origen')->default('programado'); // programado | emergencia
            $table->string('prioridad')->nullable(); // esi_1 (más urgente) .. esi_5 (menos urgente)
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internamientos');
    }
};
