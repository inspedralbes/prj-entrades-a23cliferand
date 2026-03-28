<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeientDisseny extends Model
{
    protected $table = 'seients_disseny';

    protected $fillable = [
        'sala_id',
        'fila',
        'columna',
        'etiqueta_fila',
        'num_seient',
        'es_seient',
    ];

    protected $casts = [
        'fila' => 'integer',
        'columna' => 'integer',
        'num_seient' => 'integer',
        'es_seient' => 'boolean',
    ];

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }
}
