<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quirofano extends Model
{
    protected $fillable = [
        'numero',
        'nombre',
        'estado',
    ];

    public function cirugias(): HasMany
    {
        return $this->hasMany(Cirugia::class);
    }
}
