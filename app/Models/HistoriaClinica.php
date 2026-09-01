<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoriaClinica extends Model
{
    protected $fillable = [
        'paciente_id',
        'medico_id',
        'cita_id',
        'motivo_consulta',
        'diagnostico',
        'tratamiento',
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

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    // Módulo 3 del expediente clínico completo (sección 8 de MEMORIA.md):
    // a diferencia de alergias/antecedentes (por paciente), signos vitales
    // va por consulta — 1 a 1 con esta historia clínica.
    public function signosVitales(): HasOne
    {
        return $this->hasOne(SignosVitales::class);
    }
}
