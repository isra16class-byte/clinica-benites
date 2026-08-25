<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicioAmbulancia extends Model
{
    protected $table = 'servicios_ambulancia';

    protected $fillable = [
        'paciente_id',
        'origen',
        'destino',
        'fecha_hora',
        'motivo',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}
