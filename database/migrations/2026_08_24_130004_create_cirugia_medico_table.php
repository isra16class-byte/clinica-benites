<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla pivote — médicos adicionales de una cirugía (anestesiólogo,
     * ayudantes), aparte de `cirugias.medico_principal_id` (sección 6.2 de
     * MEMORIA.md, grupo 2). `rol` es texto libre en vez de una lista fija
     * cerrada (supuesto razonable, ajustable después) para no bloquear el
     * registro si la clínica usa un rol quirúrgico que no se previó acá.
     */
    public function up(): void
    {
        Schema::create('cirugia_medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cirugia_id')->constrained('cirugias')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medicos');
            $table->string('rol')->nullable(); // ej. anestesiologo, ayudante
            $table->timestamps();

            $table->unique(['cirugia_id', 'medico_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugia_medico');
    }
};
