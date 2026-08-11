<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_transaksi', 'sewa_id', 'jenis_pembayaran', 'nominal', 'metode',
        'bukti_pembayaran', 'tanggal_bayar', 'jatuh_tempo', 'status', 'validated_by', 'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
            'jatuh_tempo' => 'date',
            'validated_at' => 'datetime',
        ];
    }

    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Jika pembayaran ini adalah tagihan perpanjangan sewa, relasi ke pengajuan perpanjangannya.
     */
    public function perpanjangan(): HasOne
    {
        return $this->hasOne(SewaPerpanjangan::class);
    }
}
