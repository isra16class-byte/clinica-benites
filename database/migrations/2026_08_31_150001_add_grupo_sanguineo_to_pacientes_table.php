<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * El grupo sanguíneo (MEMORIA.md sección 8, módulo "Antecedentes") es un
     * único dato fijo del paciente, no una lista de entradas categorizadas
     * como el resto de antecedentes — por eso vive como columna directa en
     * `pacientes`, no como fila en la tabla `antecedentes`. Nullable: no se
     * conoce de entrada para pacientes ya existentes ni para uno nuevo hasta
     * que se confirme (ej. con un examen).
     */
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('grupo_sanguineo')->nullable()->after('sexo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('grupo_sanguineo');
        });
    }
};
