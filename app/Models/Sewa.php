<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'kamar_id', 'customer_id', 'penyewa_langsung_id', 'booking_id',
        'jenis_durasi', 'tanggal_mulai', 'tanggal_selesai', 'harga', 'deposit', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'harga' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function penyewaLangsung(): BelongsTo
    {
        return $this->belongsTo(PenyewaLangsung::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function perpanjangans(): HasMany
    {
        return $this->hasMany(SewaPerpanjangan::class);
    }

    /**
     * Pengajuan perpanjangan yang masih menunggu persetujuan Owner (jika ada).
     */
    public function perpanjanganMenunggu(): HasOne
    {
        return $this->hasOne(SewaPerpanjangan::class)->where('status', 'menunggu')->latestOfMany();
    }

    /**
     * Nama penyewa. Customer wajib memiliki akun; kolom penyewaLangsung dipertahankan
     * untuk kompatibilitas data lama dan tidak lagi diisi lewat fitur aktif.
     */
    public function namaPenyewa(): string
    {
        return $this->customer->name ?? $this->penyewaLangsung->nama ?? '-';
    }

    public function whatsappPenyewa(): ?string
    {
        return $this->customer->phone ?? $this->penyewaLangsung->whatsapp ?? null;
    }

    /**
     * Total akumulasi pembayaran berstatus Lunas untuk sewa ini (Modul 5.2 Kartu Kamar Terisi).
     */
    public function totalPembayaranLunas(): float
    {
        return (float) $this->pembayarans()->where('status', 'lunas')->sum('nominal');
    }

    public function sisaHari(): int
    {
        return max(0, now()->startOfDay()->diffInDays($this->tanggal_selesai, false));
    }
}
