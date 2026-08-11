<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KamarFoto extends Model
{
    protected $table = 'kamar_fotos';

    protected $fillable = ['kamar_id', 'path', 'urutan'];

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }
}
