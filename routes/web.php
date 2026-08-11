<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\PembayaranController as CustomerPembayaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Owner\KamarController;
use App\Http\Controllers\Owner\KosController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Owner\PaymentSettingController;
use App\Http\Controllers\Owner\PembayaranValidationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\Superadmin\KosOverviewController;
use App\Http\Controllers\Superadmin\UserManagementController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\SewaPerpanjanganController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul 8: Landing Page (Guest) -- tidak perlu login
|--------------------------------------------------------------------------
*/
Route::get('/', [GuestController::class, 'landing'])->name('landing');
Route::get('/kos/{kos}', [GuestController::class, 'kosDetail'])->name('guest.kos.detail');
Route::get('/kos/{kos}/hubungi-owner', [GuestController::class, 'hubungiOwner'])->name('guest.kos.hubungi-owner');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Rute yang butuh login (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/file/ktp/{penyewaLangsung}', [SecureFileController::class, 'ktp'])->name('file.ktp');
    Route::get('/file/bukti-pembayaran/{pembayaran}', [SecureFileController::class, 'buktiPembayaran'])->name('file.bukti-pembayaran');

    Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');

    /*
    |----------------------------------------------------------------------
    | Superadmin -- Modul 5: Manajemen Akun seluruh role
    |----------------------------------------------------------------------
    */
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::patch('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
        Route::get('kos', [KosOverviewController::class, 'index'])->name('kos.index');
    });

    /*
    |----------------------------------------------------------------------
    | Owner
    |----------------------------------------------------------------------
    */
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Ditulis eksplisit satu per satu (bukan Route::resource) agar nama parameter
        // {kos} dan {kamar} pasti apa adanya -- Route::resource pernah salah "menyingularkan"
        // kata "kos" (bukan kosakata Inggris) menjadi "ko", yang menyebabkan model binding gagal.
        Route::get('kos', [KosController::class, 'index'])->name('kos.index');
        Route::get('kos/create', [KosController::class, 'create'])->name('kos.create');
        Route::post('kos', [KosController::class, 'store'])->name('kos.store');
        Route::get('kos/{kos}/edit', [KosController::class, 'edit'])->name('kos.edit');
        Route::put('kos/{kos}', [KosController::class, 'update'])->name('kos.update');
        Route::delete('kos/{kos}', [KosController::class, 'destroy'])->name('kos.destroy');

        Route::get('kos/{kos}/kamar', [KamarController::class, 'index'])->name('kos.kamar.index');
        Route::get('kos/{kos}/kamar/create', [KamarController::class, 'create'])->name('kos.kamar.create');
        Route::post('kos/{kos}/kamar', [KamarController::class, 'store'])->name('kos.kamar.store');
        Route::get('kamar/{kamar}/edit', [KamarController::class, 'edit'])->name('kamar.edit');
        Route::put('kamar/{kamar}', [KamarController::class, 'update'])->name('kamar.update');
        Route::delete('kamar/{kamar}', [KamarController::class, 'destroy'])->name('kamar.destroy');
        Route::get('kamar/{kamar}', [KamarController::class, 'show'])->name('kamar.show');
        Route::delete('kamar-foto/{kamarFoto}', [KamarController::class, 'destroyFoto'])->name('kamar.foto.destroy');

        Route::get('metode-pembayaran', [PaymentSettingController::class, 'edit'])->name('payment-settings.edit');
        Route::put('metode-pembayaran', [PaymentSettingController::class, 'update'])->name('payment-settings.update');

        Route::get('pembayaran', [PembayaranValidationController::class, 'index'])->name('pembayaran.index');
        Route::patch('pembayaran/{pembayaran}/validasi', [PembayaranValidationController::class, 'validasi'])->name('pembayaran.validasi');
        Route::patch('pembayaran/{pembayaran}/tolak', [PembayaranValidationController::class, 'tolak'])->name('pembayaran.tolak');

        Route::get('tugas/tambah', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('tugas', [TaskController::class, 'store'])->name('tasks.store');

        Route::get('pengumuman/tambah', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('pengumuman', [AnnouncementController::class, 'store'])->name('announcements.store');

        Route::delete('sewa/{sewa}', [SewaController::class, 'destroy'])->name('sewa.destroy');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

        Route::patch('perpanjangan/{perpanjangan}/setujui', [SewaPerpanjanganController::class, 'approve'])->name('perpanjangan.approve');
        Route::patch('perpanjangan/{perpanjangan}/tolak', [SewaPerpanjanganController::class, 'reject'])->name('perpanjangan.reject');
    });

    /*
    |----------------------------------------------------------------------
    | Booking, Sewa, Maintenance, Tugas -- dipakai lintas role (Owner/Penjaga/Customer)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:owner,penjaga')->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::patch('/booking/{booking}/approve', [BookingController::class, 'approve'])->name('booking.approve');
        Route::patch('/booking/{booking}/reject', [BookingController::class, 'reject'])->name('booking.reject');

        Route::get('/sewa', [SewaController::class, 'index'])->name('sewa.index');

        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::patch('/maintenance/{maintenanceReport}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');

        Route::get('/tugas', [TaskController::class, 'index'])->name('tasks.index');
        Route::patch('/tugas/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    });

    /*
    |----------------------------------------------------------------------
    | Penjaga
    |----------------------------------------------------------------------
    */
    Route::middleware('role:penjaga')->prefix('penjaga')->name('penjaga.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    /*
    |----------------------------------------------------------------------
    | Customer
    |----------------------------------------------------------------------
    */
    Route::middleware('role:customer')->group(function () {
        Route::get('customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');

        Route::get('/kamar/{kamar}/booking', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/kamar/{kamar}/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/booking/{booking}/konfirmasi', [BookingController::class, 'confirmation'])->name('booking.confirmation');

        Route::get('/sewa-saya', [SewaController::class, 'index'])->name('customer.sewa.index');
        Route::get('/sewa/{sewa}/perpanjang', [SewaPerpanjanganController::class, 'create'])->name('sewa.perpanjang.create');
        Route::post('/sewa/{sewa}/perpanjang', [SewaPerpanjanganController::class, 'store'])->name('sewa.perpanjang.store');

        Route::get('/maintenance-saya', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/riwayat', [MaintenanceController::class, 'index'])->name('customer.maintenance.index');

        Route::get('/pembayaran-saya', [CustomerPembayaranController::class, 'index'])->name('customer.pembayaran.index');
        Route::get('/pembayaran/{pembayaran}', [CustomerPembayaranController::class, 'show'])->name('customer.pembayaran.show');
        Route::post('/pembayaran/{pembayaran}/upload', [CustomerPembayaranController::class, 'uploadBukti'])->name('customer.pembayaran.upload');
    });
});
