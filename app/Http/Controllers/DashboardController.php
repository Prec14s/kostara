<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\MaintenanceReport;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            User::ROLE_SUPERADMIN => $this->superadmin(),
            User::ROLE_OWNER => $this->owner($user),
            User::ROLE_PENJAGA => $this->penjaga($user),
            default => $this->customer($user),
        };
    }

    protected function superadmin(): View
    {
        return view('dashboard.superadmin', [
            'totalUsers' => User::count(),
            'totalOwner' => User::where('role', User::ROLE_OWNER)->count(),
            'totalPenjaga' => User::where('role', User::ROLE_PENJAGA)->count(),
            'totalCustomer' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'totalKos' => \App\Models\Kos::count(),
            'recentUsers' => User::latest()->limit(8)->get(),
        ]);
    }

    protected function owner(User $owner): View
    {
        $kosIds = $owner->kosList()->pluck('id');

        $kamars = Kamar::whereIn('kos_id', $kosIds)
            ->with(['kos', 'sewaAktif.customer', 'sewaAktif.penyewaLangsung', 'sewaAktif.pembayarans'])
            ->orderBy('nomor_kamar')
            ->get();

        $stats = [
            'jumlah_kos' => $kosIds->count(),
            'jumlah_kamar' => $kamars->count(),
            'kamar_tersedia' => $kamars->where('status', 'tersedia')->count(),
            'kamar_terisi' => $kamars->where('status', 'terisi')->count(),
            'booking_baru' => \App\Models\Booking::whereIn('kamar_id', $kamars->pluck('id'))->where('status', 'menunggu')->count(),
            'sewa_aktif' => \App\Models\Sewa::whereIn('kamar_id', $kamars->pluck('id'))->where('status', 'aktif')->count(),
            'pembayaran_menunggu' => \App\Models\Pembayaran::whereHas('sewa', fn ($q) => $q->whereIn('kamar_id', $kamars->pluck('id')))
                ->where('status', 'menunggu_verifikasi')->count(),
            'maintenance_aktif' => MaintenanceReport::whereIn('kamar_id', $kamars->pluck('id'))->whereIn('status', ['menunggu', 'diproses'])->count(),
            'jatuh_tempo' => \App\Models\Sewa::whereIn('kamar_id', $kamars->pluck('id'))
                ->where('status', 'aktif')
                ->whereBetween('tanggal_selesai', [now(), now()->addDays(7)])
                ->count(),
        ];

        return view('dashboard.owner', [
            'kamars' => $kamars,
            'stats' => $stats,
        ]);
    }

    protected function penjaga(User $penjaga): View
    {
        $kamars = $penjaga->kamarDijaga()->with('kos')->get();
        $kamarIds = $kamars->pluck('id');

        return view('dashboard.penjaga', [
            'kamars' => $kamars,
            'maintenanceList' => MaintenanceReport::whereIn('kamar_id', $kamarIds)->whereIn('status', ['menunggu', 'diproses'])->latest()->get(),
            'tasks' => Task::where('penjaga_id', $penjaga->id)->where('status', '!=', 'selesai')->latest()->get(),
            'jatuhTempo' => \App\Models\Sewa::whereIn('kamar_id', $kamarIds)->where('status', 'aktif')
                ->whereBetween('tanggal_selesai', [now(), now()->addDays(7)])->get(),
        ]);
    }

    protected function customer(User $customer): View
    {
        return view('dashboard.customer', [
            'bookings' => $customer->bookings()->with('kamar.kos')->latest()->limit(5)->get(),
            'sewaAktif' => $customer->sewas()->where('status', 'aktif')->with('kamar.kos')->get(),
            'tagihan' => \App\Models\Pembayaran::whereHas('sewa', fn ($q) => $q->where('customer_id', $customer->id))
                ->whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])->latest()->get(),
        ]);
    }
}
