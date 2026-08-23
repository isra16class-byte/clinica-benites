<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
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
}
