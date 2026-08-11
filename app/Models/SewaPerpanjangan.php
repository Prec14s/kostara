<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SewaPerpanjangan extends Model
{
    protected $table = 'sewa_perpanjangans';

    protected $fillable = ['sewa_id', 'pembayaran_id', 'jenis_durasi', 'harga', 'status'];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class);
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
