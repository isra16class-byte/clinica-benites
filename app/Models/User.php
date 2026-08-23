<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol', 'medico_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isRecepcion(): bool
    {
        return $this->rol === 'recepcion';
    }

    public function isMedico(): bool
    {
        return $this->rol === 'medico';
    }

    /**
     * Vínculo opcional con el registro de Medico (tabla separada, ver
     * MEMORIA.md sección 10). Solo tiene sentido cuando rol === 'medico',
     * pero no se restringe a nivel de base de datos por si se asigna en
     * otro orden.
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }
}
