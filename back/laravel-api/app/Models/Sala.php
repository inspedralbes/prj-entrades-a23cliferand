<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'nom',
        'capacitat',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(Sessio::class, 'sala_id');
    }
}
