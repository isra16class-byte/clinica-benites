<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Conecta un usuario (users) con su registro de médico (medicos), para
     * poder filtrar "mis pacientes" cuando el usuario logueado tiene rol
     * medico (ver limitación conocida documentada en MEMORIA.md sección 10).
     * Nullable porque admin y recepción no tienen medico asociado, y porque
     * un usuario con rol medico podría crearse antes de asignarle el vínculo.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('medico_id')
                ->nullable()
                ->after('rol')
                ->constrained('medicos')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medico_id');
        });
    }
};
