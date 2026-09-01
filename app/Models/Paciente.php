<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
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
