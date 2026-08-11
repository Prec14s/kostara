<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data penyewa yang dimasukkan langsung oleh Owner (Modul 9), tanpa akun website
        Schema::create('penyewa_langsungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('kamars')->cascadeOnDelete();
            $table->foreignId('input_by')->constrained('users')->cascadeOnDelete(); // Owner yang menginput
            $table->string('nama');
            $table->string('whatsapp');
            $table->string('no_ktp')->nullable();
            $table->string('foto_ktp')->nullable(); // disimpan di disk privat (BR05)
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewa_langsungs');
    }
};
