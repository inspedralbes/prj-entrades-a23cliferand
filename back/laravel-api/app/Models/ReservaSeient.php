<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaSeient extends Model
{
    protected $table = 'reserva_seient';

    protected $fillable = [
        'reserva_id',
        'seient_sessio_id',
        'tipus_client_id',
        'preu_aplicat',
    ];

    protected $casts = [
        'preu_aplicat' => 'decimal:2',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function seientSessio(): BelongsTo
    {
        return $this->belongsTo(SeientSessio::class, 'seient_sessio_id');
    }

    public function tipusClient(): BelongsTo
    {
        return $this->belongsTo(TipusClient::class, 'tipus_client_id');
    }
}
