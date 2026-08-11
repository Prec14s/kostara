<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('sewa_id')->constrained('sewas')->cascadeOnDelete();
            $table->enum('jenis_pembayaran', ['sewa', 'deposit', 'perpanjangan'])->default('sewa');
            $table->decimal('nominal', 12, 2);
            $table->enum('metode', ['transfer_bank', 'qris'])->nullable();
            $table->string('bukti_pembayaran')->nullable(); // path file di disk privat
            $table->timestamp('tanggal_bayar')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'terlambat', 'dibatalkan'])->default('belum_bayar');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete(); // Owner yang validasi (BR14)
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
