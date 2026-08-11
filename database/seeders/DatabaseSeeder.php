<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Membuat akun contoh untuk setiap role + data kos/kamar dasar agar mudah dites.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'role' => User::ROLE_SUPERADMIN,
            'name' => 'Super Admin',
            'email' => 'superadmin@kostara.test',
            'phone' => '081200000001',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $owner = User::create([
            'role' => User::ROLE_OWNER,
            'name' => 'Budi Santoso',
            'email' => 'owner@kostara.test',
            'phone' => '081200000002',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $penjaga = User::create([
            'role' => User::ROLE_PENJAGA,
            'name' => 'Siti Aminah',
            'email' => 'penjaga@kostara.test',
            'phone' => '081200000003',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $customer = User::create([
            'role' => User::ROLE_CUSTOMER,
            'name' => 'Andi Wijaya',
            'email' => 'customer@kostara.test',
            'phone' => '081200000004',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $kos = Kos::create([
            'owner_id' => $owner->id,
            'nama' => 'Kos Melati Asri',
            'deskripsi' => 'Kos putra/putri nyaman dekat kampus dan pusat kota.',
            'alamat' => 'Jl. Melati No. 10, Yogyakarta',
            'fasilitas' => 'WiFi, dapur bersama, area parkir, laundry',
            'peraturan' => 'Jam malam pukul 22.00, dilarang membawa tamu menginap.',
            'jam_operasional' => '07.00 - 21.00',
            'kontak' => $owner->phone,
            'is_active' => true,
        ]);

        $kamarTerisi = Kamar::create([
            'kos_id' => $kos->id,
            'penjaga_id' => $penjaga->id,
            'nomor_kamar' => 'K1',
            'nama_kamar' => 'Kamar Standar',
            'deskripsi' => 'Kamar ukuran 3x3 dengan kasur dan lemari.',
            'fasilitas' => 'Kasur, lemari, meja belajar',
            'harga_harian' => 100000,
            'harga_mingguan' => 500000,
            'harga_bulanan' => 1500000,
            'harga_tahunan' => 15000000,
            'status' => 'terisi',
        ]);

        Kamar::create([
            'kos_id' => $kos->id,
            'penjaga_id' => $penjaga->id,
            'nomor_kamar' => 'K2',
            'nama_kamar' => 'Kamar Deluxe',
            'fasilitas' => 'Kasur, lemari, AC, kamar mandi dalam',
            'harga_harian' => 130000,
            'harga_mingguan' => 650000,
            'harga_bulanan' => 1900000,
            'harga_tahunan' => 20000000,
            'status' => 'tersedia',
        ]);

        $sewa = \App\Models\Sewa::create([
            'kamar_id' => $kamarTerisi->id,
            'customer_id' => $customer->id,
            'jenis_durasi' => 'bulanan',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addDays(20),
            'harga' => 1500000,
            'status' => 'aktif',
        ]);

        \App\Models\Pembayaran::create([
            'no_transaksi' => 'TRX-SEED0001',
            'sewa_id' => $sewa->id,
            'jenis_pembayaran' => 'sewa',
            'nominal' => 1500000,
            'metode' => 'transfer_bank',
            'tanggal_bayar' => now()->subMonth(),
            'status' => 'lunas',
            'validated_by' => $owner->id,
            'validated_at' => now()->subMonth(),
        ]);

        Announcement::create([
            'kos_id' => $kos->id,
            'owner_id' => $owner->id,
            'judul' => 'Pemadaman Listrik Terjadwal',
            'isi' => 'Akan ada pemadaman listrik pada Sabtu, 15 Agustus 2026 pukul 09.00-12.00.',
        ]);

        $this->command?->info('Akun contoh berhasil dibuat:');
        $this->command?->table(['Role', 'Email', 'Password'], [
            ['Superadmin', 'superadmin@kostara.test', 'password'],
            ['Owner', 'owner@kostara.test', 'password'],
            ['Penjaga', 'penjaga@kostara.test', 'password'],
            ['Customer', 'customer@kostara.test', 'password'],
        ]);
    }
}
