<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenEstudio extends Model
{
    protected $table = 'ordenes_estudio';

    protected $fillable = [
        'paciente_id',
        'medico_solicitante_id',
        'cita_id',
        'tipo',
        'fecha_solicitud',
        'fecha_realizacion',
        'estado',
        'resultado_texto',
        'resultado_archivo',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'datetime',
            'fecha_realizacion' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medicoSolicitante(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_solicitante_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }
}
