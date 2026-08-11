<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';

    public const ROLE_OWNER = 'owner';

    public const ROLE_PENJAGA = 'penjaga';

    public const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'phone',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'qris_image',
        'terima_tunai',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'terima_tunai' => 'boolean',
        ];
    }

    public function kosList(): HasMany
    {
        return $this->hasMany(Kos::class, 'owner_id');
    }

    public function kamarDijaga(): HasMany
    {
        return $this->hasMany(Kamar::class, 'penjaga_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function sewas(): HasMany
    {
        return $this->hasMany(Sewa::class, 'customer_id');
    }

    public function isSuperadmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isPenjaga(): bool
    {
        return $this->role === self::ROLE_PENJAGA;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Membuat URL wa.me dengan pesan yang sudah terisi (BR08: link manual, tidak terkirim otomatis).
     */
    public function waLink(string $message): string
    {
        $number = preg_replace('/\D/', '', $this->phone ?? config('services.kostara.default_wa', ''));
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    /**
     * Owner mempunyai metode pembayaran (rekening bank dan/atau QRIS) jika salah satunya terisi.
     */
    public function punyaMetodePembayaran(): bool
    {
        return filled($this->bank_account_number) || filled($this->qris_image) || $this->terima_tunai;
    }
}
