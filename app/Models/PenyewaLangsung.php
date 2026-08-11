<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyewaLangsung extends Model
{
    use HasFactory;

    protected $fillable = [
        'kamar_id', 'input_by', 'nama', 'whatsapp', 'no_ktp', 'foto_ktp', 'alamat', 'catatan',
    ];

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function inputOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
