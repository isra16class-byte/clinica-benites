<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Segundo módulo del expediente clínico completo (MEMORIA.md sección 8,
     * confirmado por el cliente en la entrevista del 25 ago 2026). Igual que
     * Alergia, va por paciente, no por consulta — mismo razonamiento: un
     * antecedente (ej. "diabetes tipo 2", "apendicectomía 2015") es un dato
     * permanente del paciente, no algo específico de una historia clínica
     * puntual.
     */
    public function up(): void
    {
        Schema::create('antecedentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('categoria'); // personal | quirurgico | familiar | habito
            $table->text('descripcion'); // ej. "Diabetes tipo 2", "Apendicectomía (2015)", "Madre con hipertensión", "Fuma 10 cigarrillos/día"
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedentes');
    }
};
