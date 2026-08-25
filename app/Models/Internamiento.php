<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Internamiento extends Model
{
    protected $fillable = [
        'paciente_id',
        'cama_id',
        'medico_id',
        'cita_id',
        'fecha_ingreso',
        'fecha_alta',
        'motivo',
        'origen',
        'prioridad',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'datetime',
            'fecha_alta' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cama(): BelongsTo
    {
        return $this->belongsTo(Cama::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function activo(): bool
    {
        return $this->fecha_alta === null;
    }
}
