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
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function historiaClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
