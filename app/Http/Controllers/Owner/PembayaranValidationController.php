<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembayaranValidationController extends Controller
{
    /**
     * Modul 14.3: daftar pembayaran yang menunggu validasi Owner, mirip notifikasi
     * "Sudah cek rekening Anda? Tekan Ya jika sudah menerima."
     */
    public function index(Request $request): View
    {
        $kamarIds = \App\Models\Kamar::whereHas('kos', fn ($q) => $q->where('owner_id', $request->user()->id))->pluck('id');

        $pembayarans = Pembayaran::whereHas('sewa', fn ($q) => $q->whereIn('kamar_id', $kamarIds))
            ->with(['sewa.kamar.kos', 'sewa.customer', 'sewa.penyewaLangsung'])
            ->latest()
            ->paginate(15);

        $perpanjanganMenunggu = \App\Models\SewaPerpanjangan::where('status', 'menunggu')
            ->whereHas('sewa', fn ($q) => $q->whereIn('kamar_id', $kamarIds))
            ->with(['sewa.kamar', 'sewa.customer', 'sewa.penyewaLangsung'])
            ->latest()
            ->get();

        return view('owner.pembayaran.index', ['pembayarans' => $pembayarans, 'perpanjanganMenunggu' => $perpanjanganMenunggu]);
    }

    /**
     * Owner menekan "Ya, Validasi" setelah memeriksa rekening/QRIS secara manual (BR14).
     */
    public function validasi(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless((int) $pembayaran->sewa->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Pembayaran ini bukan milik kos Anda.');

        DB::transaction(function () use ($pembayaran, $request) {
            $pembayaran->update([
                'status' => 'lunas',
                'validated_by' => $request->user()->id,
                'validated_at' => now(),
            ]);

            $sewa = $pembayaran->sewa;
            if ($sewa->status === 'menunggu_pembayaran') {
                $sewa->update(['status' => 'aktif']);
                $sewa->kamar->update(['status' => 'terisi']);
            }

            // Jika ini tagihan perpanjangan yang sudah disetujui Owner, baru sekarang
            // tanggal_selesai sewa benar-benar bertambah (BR10) -- bukan saat pengajuan/persetujuan.
            $perpanjangan = $pembayaran->perpanjangan;
            if ($perpanjangan && $perpanjangan->status === 'disetujui') {
                $tambahan = match ($perpanjangan->jenis_durasi) {
                    'harian' => 1,
                    'mingguan' => 7,
                    'bulanan' => 30,
                    'tahunan' => 365,
                };

                $sewa->update([
                    'tanggal_selesai' => $sewa->tanggal_selesai->copy()->addDays($tambahan),
                    'status' => 'aktif',
                ]);
            }
        });

        return back()->with('status', 'Pembayaran berhasil divalidasi. Status berubah menjadi Lunas.');
    }

    /**
     * Owner menekan "Belum" -- status tetap Menunggu Verifikasi.
     */
    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless((int) $pembayaran->sewa->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Pembayaran ini bukan milik kos Anda.');

        $pembayaran->update(['status' => 'menunggu_verifikasi']);

        return back()->with('status', 'Pembayaran ditandai belum diterima. Menunggu bukti/dana lebih lanjut.');
    }
}
