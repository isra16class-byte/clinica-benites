<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Primer módulo del expediente clínico completo (MEMORIA.md sección 8,
     * confirmado por el cliente en la entrevista del 25 ago 2026). Va por
     * paciente, no por consulta: una alergia no depende de qué historia
     * clínica se esté registrando, es un dato permanente del paciente que
     * debe verse en cualquier consulta futura. Orden de construcción
     * (alergias → antecedentes → signos vitales) elegido por seguridad del
     * paciente primero.
     */
    public function up(): void
    {
        Schema::create('alergias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('alergeno'); // qué causa la alergia (ej. "Penicilina", "Maní")
            $table->string('tipo'); // medicamento | alimento | otro
            $table->string('severidad'); // leve | moderada | severa
            $table->text('reaccion')->nullable(); // qué reacción produce (ronchas, anafilaxia, etc.)
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alergias');
    }
};
