<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->foreignId('penjaga_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_kamar');
            $table->string('nama_kamar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('fasilitas')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('harga_harian', 12, 2)->nullable();
            $table->decimal('harga_mingguan', 12, 2)->nullable();
            $table->decimal('harga_bulanan', 12, 2)->nullable();
            $table->decimal('harga_tahunan', 12, 2)->nullable();
            $table->enum('status', ['tersedia', 'dipesan', 'terisi', 'maintenance', 'nonaktif'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
