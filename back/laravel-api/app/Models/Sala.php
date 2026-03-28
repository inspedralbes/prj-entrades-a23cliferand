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
        'files_max',
        'columnes_max',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(Sessio::class, 'sala_id');
    }

    public function seientsDisseny(): HasMany
    {
        return $this->hasMany(SeientDisseny::class, 'sala_id');
    }

    public function seients(): HasMany
    {
        return $this->hasMany(SeientDisseny::class, 'sala_id')->where('es_seient', true);
    }
}
