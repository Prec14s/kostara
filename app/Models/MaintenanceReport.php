<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'kamar_id', 'customer_id', 'handled_by', 'jenis_masalah', 'deskripsi', 'foto', 'prioritas', 'status',
    ];

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function penjaga(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
