<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuari extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuaris';

    protected $fillable = [
        'nom',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reserves(): HasMany
    {
        return $this->hasMany(Reserva::class, 'usuari_id');
    }
}
