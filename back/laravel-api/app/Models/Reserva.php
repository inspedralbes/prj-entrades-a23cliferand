<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reserva extends Model
{
    protected $table = 'reserva';

    protected $fillable = [
        'usuari_id',
        'sessio_id',
        'preu_total',
        'estat',
    ];

    protected $casts = [
        'preu_total' => 'decimal:2',
    ];

    protected $enumEstat = ['pendent', 'confirmada', 'caducada'];

    public function usuari(): BelongsTo
    {
        return $this->belongsTo(Usuari::class, 'usuari_id');
    }

    public function sessio(): BelongsTo
    {
        return $this->belongsTo(Sessio::class, 'sessio_id');
    }

    public function seientsSessio(): BelongsToMany
    {
        return $this->belongsToMany(SeientSessio::class, 'reserva_seient')
                    ->withPivot('tipus_client_id', 'preu_aplicat')
                    ->withTimestamps();
    }
}
