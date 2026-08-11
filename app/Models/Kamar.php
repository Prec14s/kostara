<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kos_id', 'penjaga_id', 'nomor_kamar', 'nama_kamar', 'deskripsi', 'fasilitas', 'foto',
        'harga_harian', 'harga_mingguan', 'harga_bulanan', 'harga_tahunan', 'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_harian' => 'decimal:2',
            'harga_mingguan' => 'decimal:2',
            'harga_bulanan' => 'decimal:2',
            'harga_tahunan' => 'decimal:2',
        ];
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    public function penjaga(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penjaga_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function sewas(): HasMany
    {
        return $this->hasMany(Sewa::class);
    }

    public function penyewaLangsungs(): HasMany
    {
        return $this->hasMany(PenyewaLangsung::class);
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    /**
     * Galeri foto kamar (disarankan minimal 4 foto) agar Customer bisa menilai kamar sebelum booking.
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(KamarFoto::class)->orderBy('urutan');
    }

    /**
     * Foto sampul untuk thumbnail di daftar/kartu kamar -- foto galeri pertama,
     * dengan fallback ke kolom foto lama jika galeri belum diisi.
     */
    public function coverFoto(): ?string
    {
        return $this->fotos->first()->path ?? $this->foto ?? null;
    }

    /**
     * Sewa yang sedang aktif pada kamar ini (dipakai untuk kartu "Kamar Terisi" di dashboard Owner).
     */
    public function sewaAktif(): HasOne
    {
        return $this->hasOne(Sewa::class)->where('status', 'aktif')->latestOfMany();
    }

    public function hargaUntuk(string $durasi): ?float
    {
        return match ($durasi) {
            'harian' => $this->harga_harian,
            'mingguan' => $this->harga_mingguan,
            'bulanan' => $this->harga_bulanan,
            'tahunan' => $this->harga_tahunan,
            default => null,
        };
    }
}
