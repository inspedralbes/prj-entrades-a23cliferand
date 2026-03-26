<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    protected $table = 'tarifes';

    protected $fillable = [
        'nom',
    ];

    public function preusTarifa(): HasMany
    {
        return $this->hasMany(PreuTarifa::class, 'tarifa_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Sessio::class, 'tarifa_id');
    }
}
