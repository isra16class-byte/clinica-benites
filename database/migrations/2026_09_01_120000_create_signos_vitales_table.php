<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tercer y último módulo del expediente clínico completo (MEMORIA.md
     * sección 8, confirmado por el cliente en la entrevista del 25 ago
     * 2026). A diferencia de Alergias y Antecedentes (que van por
     * paciente), este va **por consulta** — relación 1 a 1 con
     * `historia_clinicas` (`historia_clinica_id` con `unique()`), porque los
     * signos vitales tienen sentido en el momento puntual de esa consulta,
     * no como dato permanente del paciente. Todos los campos nullable: no
     * todas las consultas necesariamente miden los 7 (ej. una consulta de
     * seguimiento por resultados de laboratorio puede no requerir tomar
     * peso/talla de nuevo).
     */
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->unique()->constrained('historia_clinicas')->cascadeOnDelete();
            $table->string('presion_arterial')->nullable(); // formato libre, ej. "120/80"
            $table->decimal('temperatura', 4, 1)->nullable(); // °C
            $table->unsignedSmallInteger('frecuencia_cardiaca')->nullable(); // lpm
            $table->unsignedSmallInteger('frecuencia_respiratoria')->nullable(); // rpm
            $table->decimal('peso', 5, 2)->nullable(); // kg
            $table->decimal('talla', 5, 2)->nullable(); // cm
            $table->unsignedTinyInteger('saturacion_oxigeno')->nullable(); // %
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};
