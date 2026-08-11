<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengajuan perpanjangan sewa oleh Customer. Alurnya dua tahap:
     * 1) Customer ajukan durasi -> menunggu persetujuan Owner.
     * 2) Owner setuju -> tagihan pembayaran dibuat -> Customer bayar -> Owner validasi pembayaran ->
     *    baru tanggal_selesai sewa benar-benar bertambah.
     */
    public function up(): void
    {
        Schema::create('sewa_perpanjangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_id')->constrained('sewas')->cascadeOnDelete();
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayarans')->nullOnDelete();
            $table->enum('jenis_durasi', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->decimal('harga', 12, 2);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa_perpanjangans');
    }
};
