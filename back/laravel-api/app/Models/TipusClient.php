<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipusClient extends Model
{
    protected $table = 'tipus_client';

    protected $fillable = [
        'nom',
    ];

    public function preusTarifa(): HasMany
    {
        return $this->hasMany(PreuTarifa::class, 'tipus_client_id');
    }

    public function reservaSeients(): HasMany
    {
        return $this->hasMany(ReservaSeient::class, 'tipus_client_id');
    }
}
