<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('kamars')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penyewa_langsung_id')->nullable()->constrained('penyewa_langsungs')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->enum('jenis_durasi', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('harga', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->enum('status', ['menunggu_pembayaran', 'aktif', 'akan_berakhir', 'selesai', 'dibatalkan'])->default('menunggu_pembayaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};
