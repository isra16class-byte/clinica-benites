<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Movimientos de inventario (sección 6.3 de MEMORIA.md). Es el registro
     * fuente-de-verdad (ledger): toda entrada, salida, traslado o ajuste de
     * stock queda como una fila acá, incluida la carga inicial de un lote
     * (se registra como un movimiento de tipo "entrada", no como una
     * columna `cantidad` editable a mano en el lote).
     *
     * `cita_id` es el enganche con consumo real de un paciente durante su
     * atención (ej. un insumo usado en una cita). No se referencia todavía
     * ninguna `cirugia_id` porque esa tabla (sección 6.2) no existe aún —
     * este módulo se construyó de forma independiente, como quedó abierto
     * en las decisiones pendientes de 6.3. Se puede agregar esa columna más
     * adelante sin romper nada de esto.
     */
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes_inventario');
            $table->string('tipo_movimiento'); // entrada | salida | traslado | ajuste
            $table->decimal('cantidad', 10, 2);
            $table->string('area_origen')->nullable();
            $table->string('area_destino')->nullable();
            $table->dateTime('fecha_hora');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes');
            $table->foreignId('cita_id')->nullable()->constrained('citas');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
