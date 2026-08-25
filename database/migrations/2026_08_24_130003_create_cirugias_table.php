<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cirugías (sección 6.2 de MEMORIA.md, grupo 2) — agenda de quirófano,
     * similar a `Cita` pero con sus propios campos. `medico_principal_id`
     * es el cirujano responsable; médicos adicionales (anestesiólogo,
     * ayudantes) se modelan aparte en `cirugia_medico` (ver esa migración)
     * en vez de columnas fijas, porque una cirugía puede tener más de uno
     * — punto abierto ya identificado en la propuesta original.
     *
     * `cita_id` nullable: se asume (supuesto razonable, sin confirmar con
     * la clínica — pregunta pendiente en sección 6.2) que una cirugía
     * puede agendarse directo sin pasar por `Cita`, igual que Factura y
     * HistoriaClinica ya tratan `cita_id` como opcional.
     */
    public function up(): void
    {
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('quirofano_id')->constrained('quirofanos');
            $table->foreignId('medico_principal_id')->constrained('medicos');
            $table->foreignId('cita_id')->nullable()->constrained('citas');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('tipo_cirugia');
            $table->string('estado')->default('programada'); // programada | en_curso | completada | cancelada
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
