<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Laporan pemasukan (Modul 25 -- Laporan Pendapatan): harian, mingguan, bulanan, tahunan,
     * dihitung dari seluruh pembayaran berstatus Lunas pada kamar-kamar milik Owner.
     */
    public function index(Request $request): View
    {
        $owner = $request->user();
        $kamarIds = Kamar::whereHas('kos', fn ($q) => $q->where('owner_id', $owner->id))->pluck('id');

        $lunasQuery = function () use ($kamarIds) {
            return Pembayaran::whereHas('sewa', fn ($q) => $q->whereIn('kamar_id', $kamarIds))
                ->where('status', 'lunas');
        };

        $hariIni = (float) $lunasQuery()->whereDate('tanggal_bayar', today())->sum('nominal');
        $mingguIni = (float) $lunasQuery()->whereBetween('tanggal_bayar', [now()->startOfWeek(), now()->endOfWeek()])->sum('nominal');
        $bulanIni = (float) $lunasQuery()->whereMonth('tanggal_bayar', now()->month)->whereYear('tanggal_bayar', now()->year)->sum('nominal');
        $tahunIni = (float) $lunasQuery()->whereYear('tanggal_bayar', now()->year)->sum('nominal');

        // Pemasukan per bulan sepanjang tahun berjalan -- untuk grafik batang.
        $perBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $perBulan[$m] = (float) $lunasQuery()->whereMonth('tanggal_bayar', $m)->whereYear('tanggal_bayar', now()->year)->sum('nominal');
        }

        // Pemasukan 7 hari terakhir -- untuk grafik batang harian.
        $per7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $per7Hari[$tanggal->format('D, d M')] = (float) $lunasQuery()->whereDate('tanggal_bayar', $tanggal)->sum('nominal');
        }

        $totalTransaksi = $lunasQuery()->count();
        $transaksiTerbaru = $lunasQuery()->with(['sewa.kamar.kos', 'sewa.customer', 'sewa.penyewaLangsung'])
            ->latest('tanggal_bayar')->limit(8)->get();

        return view('owner.laporan.index', [
            'hariIni' => $hariIni,
            'mingguIni' => $mingguIni,
            'bulanIni' => $bulanIni,
            'tahunIni' => $tahunIni,
            'perBulan' => $perBulan,
            'per7Hari' => $per7Hari,
            'totalTransaksi' => $totalTransaksi,
            'transaksiTerbaru' => $transaksiTerbaru,
        ]);
    }
}
