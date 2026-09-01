<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    /**
     * Tipo de identificación para facturación electrónica SRI (MEMORIA.md
     * sección 6). Clave interna => [código SRI, etiqueta]. El código SRI
     * es el que exige dazza-dev/sri-xml-generator en `Customer` (verificado
     * contra `identification-types.json` del paquete real). No se ofrece
     * "identificación del exterior" (código 08 SRI): caso de borde no
     * relevante para pacientes de la clínica.
     */
    public const TIPOS_IDENTIFICACION = [
        'cedula' => ['sri' => '05', 'label' => 'Cédula'],
        'ruc' => ['sri' => '04', 'label' => 'RUC'],
        'pasaporte' => ['sri' => '06', 'label' => 'Pasaporte'],
        'consumidor_final' => ['sri' => '07', 'label' => 'Consumidor final'],
    ];

    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'tipo_identificacion',
        'fecha_nacimiento',
        'telefono',
        'email',
        'direccion',
        'sexo',
        'grupo_sanguineo',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function historiaClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    // Expediente clínico completo (sección 8 de MEMORIA.md): alergias vive
    // por paciente, no por consulta.
    public function alergias(): HasMany
    {
        return $this->hasMany(Alergia::class);
    }

    // Módulo 2 del expediente clínico completo (sección 8 de MEMORIA.md):
    // igual que alergias, vive por paciente. `grupo_sanguineo` (dato único,
    // no una lista) es una columna directa de esta tabla, ver migración.
    public function antecedentes(): HasMany
    {
        return $this->hasMany(Antecedente::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Relaciones de la infraestructura física (sección 6.2 de MEMORIA.md).
    public function internamientos(): HasMany
    {
        return $this->hasMany(Internamiento::class);
    }

    public function cirugias(): HasMany
    {
        return $this->hasMany(Cirugia::class);
    }

    public function ordenesEstudio(): HasMany
    {
        return $this->hasMany(OrdenEstudio::class);
    }

    public function serviciosAmbulancia(): HasMany
    {
        return $this->hasMany(ServicioAmbulancia::class);
    }
}
