<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Facturación electrónica SRI (MEMORIA.md sección 6): el comprobante
     * exige declarar el tipo de identificación del comprador con uno de los
     * códigos fijos del catálogo oficial (04 RUC, 05 Cédula, 06 Pasaporte,
     * 07 Consumidor final — verificado contra el código fuente real de
     * dazza-dev/sri-xml-generator, `src/Data/identification-types.json`,
     * clonado en el sandbox). No se agrega "08 Identificación del exterior"
     * como opción del formulario: es para comprador extranjero sin RUC/
     * pasaporte, caso de borde no relevante para pacientes de la clínica.
     *
     * No se toca la columna `cedula` existente (unique, usada en todo el
     * proyecto): sigue siendo el número de identificación tal cual, para
     * cédula o RUC — un RUC de persona natural en Ecuador son los mismos 10
     * dígitos de la cédula + "001", así que en la práctica un paciente con
     * RUC puede seguir escribiendo ese número completo ahí. Ver nota en
     * PacienteForm.
     */
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('tipo_identificacion')->default('cedula')->after('cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('tipo_identificacion');
        });
    }
};
