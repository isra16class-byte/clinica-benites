<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignosVitales extends Model
{
    protected $table = 'signos_vitales';

    protected $fillable = [
        'historia_clinica_id',
        'presion_arterial',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'peso',
        'talla',
        'saturacion_oxigeno',
        'notas',
    ];

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class);
    }
}
