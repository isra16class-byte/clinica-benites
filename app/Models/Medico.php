<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $fillable = [
        'nombres',
        'apellidos',
        'area_id',
        'telefono',
        'email',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function historiaClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    // Relaciones de la infraestructura física (sección 6.2 de MEMORIA.md).
    public function internamientos(): HasMany
    {
        return $this->hasMany(Internamiento::class);
    }

    public function cirugiasComoResponsable(): HasMany
    {
        return $this->hasMany(Cirugia::class, 'medico_principal_id');
    }

    public function ordenesEstudioSolicitadas(): HasMany
    {
        return $this->hasMany(OrdenEstudio::class, 'medico_solicitante_id');
    }
}
