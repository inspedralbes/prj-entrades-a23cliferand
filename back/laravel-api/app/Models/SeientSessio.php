<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SeientSessio extends Model
{
    protected $table = 'seients_sessio';

    protected $fillable = [
        'sessio_id',
        'fila',
        'numero',
        'estat',
        'reservat_at',
    ];

    protected $casts = [
        'reservat_at' => 'datetime',
    ];

    protected $enumEstat = ['lliure', 'reservat', 'venut'];

    public function sessio(): BelongsTo
    {
        return $this->belongsTo(Sessio::class, 'sessio_id');
    }

    public function reserves(): BelongsToMany
    {
        return $this->belongsToMany(Reserva::class, 'reserva_seient')
                    ->withPivot('tipus_client_id', 'preu_aplicat')
                    ->withTimestamps();
    }

    public function estaExpirat(): bool
    {
        if ($this->estat !== 'reservat' || is_null($this->reservat_at)) {
            return false;
        }

        return $this->reservat_at->addMinutes(5)->isPast();
    }
}
