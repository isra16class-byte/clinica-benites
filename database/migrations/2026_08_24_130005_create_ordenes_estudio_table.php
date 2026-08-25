<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Órdenes de estudio (sección 6.2 de MEMORIA.md, grupo 3) — modelo
     * unificado para Laboratorio, Rayos X, Ecografía, Centro de Imagen,
     * Unidad de Endoscopía (alta y baja), Centro de Gastroenterología y
     * Procedimientos Ambulatorios, en vez de una tabla por tipo de estudio:
     * todos comparten el mismo patrón (un médico solicita, el paciente lo
     * hace, hay un resultado).
     *
     * `resultado_texto` + `resultado_archivo` (path nullable, sección
     * 6.2 dejaba esto como pregunta abierta): se asume (supuesto razonable)
     * texto libre desde el día uno más adjunto opcional vía el disco local
     * de Sail — no se evaluó todavía almacenamiento externo tipo S3, se
     * puede migrar después sin romper lo guardado (la columna solo guarda
     * la ruta, no el mecanismo de almacenamiento).
     */
    public function up(): void
    {
        Schema::create('ordenes_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('medico_solicitante_id')->constrained('medicos');
            $table->foreignId('cita_id')->nullable()->constrained('citas');
            $table->string('tipo'); // laboratorio | rayos_x | ecografia | centro_imagen | endoscopia_alta | endoscopia_baja | gastroenterologia | procedimiento_ambulatorio
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_realizacion')->nullable();
            $table->string('estado')->default('solicitado'); // solicitado | en_proceso | completado
            $table->text('resultado_texto')->nullable();
            $table->string('resultado_archivo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_estudio');
    }
};
