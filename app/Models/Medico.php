<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
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
}
