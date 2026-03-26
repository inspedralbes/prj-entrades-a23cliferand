<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreuTarifa extends Model
{
    protected $table = 'preus_tarifa';

    protected $fillable = [
        'tarifa_id',
        'tipus_client_id',
        'preu',
    ];

    protected $casts = [
        'preu' => 'decimal:2',
    ];

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    public function tipusClient(): BelongsTo
    {
        return $this->belongsTo(TipusClient::class, 'tipus_client_id');
    }
}
