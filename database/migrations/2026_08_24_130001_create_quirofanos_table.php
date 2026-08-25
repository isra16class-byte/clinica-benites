<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Quirófanos (sección 6.2 de MEMORIA.md, grupo 2). A diferencia de
     * `camas`, sí tiene una columna `estado` guardada en vez de derivada:
     * el ciclo de un quirófano (preparación → en cirugía → limpieza →
     * libre) no se puede inferir solo de si hay una cirugía activa, porque
     * el tiempo de limpieza entre una cirugía y la siguiente es un estado
     * real y no instantáneo (ajuste validado con investigación externa,
     * 24 ago 2026 — ver sección 6.2).
     */
    public function up(): void
    {
        Schema::create('quirofanos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nombre')->nullable();
            $table->string('estado')->default('libre'); // preparacion | en_cirugia | limpieza | libre
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quirofanos');
    }
};
