<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }
}
