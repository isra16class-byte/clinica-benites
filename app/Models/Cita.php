<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cita extends Model
{
    protected $fillable = [
        'paciente_id',
        'medico_id',
        'area_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'origen',
        'prioridad',
        'notas',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
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
}
