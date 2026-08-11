<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Sewa;
use App\Models\SewaPerpanjangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SewaPerpanjanganController extends Controller
{
    /**
     * Customer memilih durasi perpanjangan (harian/mingguan/bulanan/tahunan) -- BR10.
     */
    public function create(Request $request, Sewa $sewa): View
    {
        abort_unless((int) $sewa->customer_id === (int) $request->user()->id, 403, 'Sewa ini bukan milik Anda.');
        abort_unless($sewa->status === 'aktif', 404);
        abort_if($sewa->perpanjangans()->where('status', 'menunggu')->exists(), 404);

        $sewa->load('kamar');

        return view('sewa.perpanjang', ['sewa' => $sewa]);
    }

    /**
     * Simpan pengajuan -- BELUM langsung menambah tanggal_selesai. Menunggu Owner menyetujui.
     */
    public function store(Request $request, Sewa $sewa): RedirectResponse
    {
        abort_unless((int) $sewa->customer_id === (int) $request->user()->id, 403, 'Sewa ini bukan milik Anda.');
        abort_unless($sewa->status === 'aktif', 422, 'Hanya sewa aktif yang dapat diperpanjang.');
        abort_if($sewa->perpanjangans()->where('status', 'menunggu')->exists(), 422, 'Sudah ada pengajuan perpanjangan yang menunggu persetujuan Owner.');

        $data = $request->validate([
            'jenis_durasi' => ['required', 'in:harian,mingguan,bulanan,tahunan'],
        ]);

        $harga = $sewa->kamar->hargaUntuk($data['jenis_durasi']) ?? $sewa->harga;

        SewaPerpanjangan::create([
            'sewa_id' => $sewa->id,
            'jenis_durasi' => $data['jenis_durasi'],
            'harga' => $harga,
            'status' => 'menunggu',
        ]);

        return redirect()->route('customer.sewa.index')
            ->with('status', 'Pengajuan perpanjangan sewa terkirim. Menunggu persetujuan Owner sebelum tagihan dibuat.');
    }

    /**
     * Owner menyetujui pengajuan -- baru di sini tagihan pembayaran (belum_bayar) dibuat untuk Customer.
     * Tanggal sewa TIDAK langsung bertambah; itu baru terjadi setelah Owner memvalidasi pembayarannya.
     */
    public function approve(Request $request, SewaPerpanjangan $perpanjangan): RedirectResponse
    {
        abort_unless((int) $perpanjangan->sewa->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Sewa ini bukan milik kos Anda.');
        abort_unless($perpanjangan->status === 'menunggu', 422, 'Pengajuan ini sudah diproses sebelumnya.');

        DB::transaction(function () use ($perpanjangan) {
            $pembayaran = Pembayaran::create([
                'no_transaksi' => 'TRX-'.Str::upper(Str::random(8)),
                'sewa_id' => $perpanjangan->sewa_id,
                'jenis_pembayaran' => 'perpanjangan',
                'nominal' => $perpanjangan->harga,
                'status' => 'belum_bayar',
            ]);

            $perpanjangan->update([
                'status' => 'disetujui',
                'pembayaran_id' => $pembayaran->id,
            ]);
        });

        return back()->with('status', 'Pengajuan perpanjangan disetujui. Tagihan pembayaran telah dibuat untuk Customer.');
    }

    public function reject(Request $request, SewaPerpanjangan $perpanjangan): RedirectResponse
    {
        abort_unless((int) $perpanjangan->sewa->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Sewa ini bukan milik kos Anda.');
        abort_unless($perpanjangan->status === 'menunggu', 422, 'Pengajuan ini sudah diproses sebelumnya.');

        $perpanjangan->update(['status' => 'ditolak']);

        return back()->with('status', 'Pengajuan perpanjangan ditolak.');
    }
}
