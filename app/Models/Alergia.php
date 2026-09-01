<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alergia extends Model
{
    protected $fillable = [
        'paciente_id',
        'alergeno',
        'tipo',
        'severidad',
        'reaccion',
        'notas',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}
