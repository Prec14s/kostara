<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan opsi pembayaran Tunai (Cash) di samping Transfer Bank dan QRIS.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('terima_tunai')->default(false)->after('qris_image');
        });

        // Perluas enum kolom metode di tabel pembayarans agar mendukung 'tunai'.
        // Pakai raw statement (bukan Schema::table->enum) supaya tidak perlu doctrine/dbal.
        DB::statement("ALTER TABLE pembayarans MODIFY metode ENUM('transfer_bank', 'qris', 'tunai') NULL");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('terima_tunai');
        });

        DB::statement("ALTER TABLE pembayarans MODIFY metode ENUM('transfer_bank', 'qris') NULL");
    }
};
