<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sessio extends Model
{
    protected $table = 'sessions_cine';

    protected $fillable = [
        'pellicula_id',
        'sala_id',
        'tarifa_id',
        'data_hora',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    public function seientsSessio(): HasMany
    {
        return $this->hasMany(SeientSessio::class, 'sessio_id');
    }

    public function reserves(): HasMany
    {
        return $this->hasMany(Reserva::class, 'sessio_id');
    }
}
