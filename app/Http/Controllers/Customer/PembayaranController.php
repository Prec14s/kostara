<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    /**
     * Modul 14.2: Customer melihat tagihan dan metode pembayaran Owner (rekening/QRIS).
     */
    public function index(Request $request): View
    {
        $pembayarans = Pembayaran::whereHas('sewa', fn ($q) => $q->where('customer_id', $request->user()->id))
            ->with(['sewa.kamar.kos.owner'])
            ->latest()
            ->get();

        return view('customer.pembayaran.index', ['pembayarans' => $pembayarans]);
    }

    public function show(Request $request, Pembayaran $pembayaran): View
    {
        abort_unless($pembayaran->sewa->customer_id === $request->user()->id, 403);
        $pembayaran->load('sewa.kamar.kos.owner');

        return view('customer.pembayaran.show', ['pembayaran' => $pembayaran]);
    }

    /**
     * Customer mengunggah bukti pembayaran (transfer/QRIS), atau menandai sudah bayar Tunai
     * (tanpa perlu bukti foto, karena serah terima langsung) -- status berubah ke Menunggu Verifikasi.
     */
    public function uploadBukti(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless($pembayaran->sewa->customer_id === $request->user()->id, 403);

        $data = $request->validate([
            'metode' => ['required', 'in:transfer_bank,qris,tunai'],
            'bukti_pembayaran' => ['required_unless:metode,tunai', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'private');
        }

        $pembayaran->update([
            'metode' => $data['metode'],
            'bukti_pembayaran' => $data['bukti_pembayaran'] ?? null,
            'tanggal_bayar' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        $pesan = $data['metode'] === 'tunai'
            ? 'Pembayaran tunai ditandai terkirim. Menunggu konfirmasi Owner setelah uang diterima.'
            : 'Bukti pembayaran terkirim. Menunggu validasi dari Owner.';

        return redirect()->route('customer.pembayaran.index')->with('status', $pesan);
    }
}
