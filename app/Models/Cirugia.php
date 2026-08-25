<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cirugia extends Model
{
    protected $fillable = [
        'paciente_id',
        'quirofano_id',
        'medico_principal_id',
        'cita_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'tipo_cirugia',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function quirofano(): BelongsTo
    {
        return $this->belongsTo(Quirofano::class);
    }

    public function medicoPrincipal(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_principal_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    /**
     * Médicos adicionales (anestesiólogo, ayudantes), aparte del cirujano
     * principal — ver `cirugia_medico` (sección 6.2 de MEMORIA.md, grupo 2).
     */
    public function medicosAdicionales(): BelongsToMany
    {
        return $this->belongsToMany(Medico::class, 'cirugia_medico')
            ->withPivot('rol')
            ->withTimestamps();
    }
}
