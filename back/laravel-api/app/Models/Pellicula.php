<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pellicula extends Model
{
    protected $table = 'pellicules';

    protected $fillable = [
        'titol',
        'descripcio',
        'duracio',
        'cartell',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(Sessio::class, 'pellicula_id');
    }
}
