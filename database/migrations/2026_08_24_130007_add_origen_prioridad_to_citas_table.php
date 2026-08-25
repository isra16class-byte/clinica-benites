<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega `origen`/`prioridad` a `citas`, mismas columnas que se
     * agregaron a `internamientos` (sección 6.2 de MEMORIA.md, grupo 4 —
     * Emergencias). Una emergencia no siempre termina en internamiento:
     * puede resolverse como una consulta (Cita) sin necesidad de cama, así
     * que el campo de origen/prioridad tiene que poder vivir en cualquiera
     * de los dos lugares.
     */
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->string('origen')->default('programada')->after('estado'); // programada | emergencia
            $table->string('prioridad')->nullable()->after('origen'); // esi_1 (más urgente) .. esi_5 (menos urgente)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['origen', 'prioridad']);
        });
    }
};
