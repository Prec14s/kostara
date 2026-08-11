<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
    /**
     * Modul 8: Landing page -- langsung menampilkan kamar-kamar kosong milik Owner
     * (sistem ini untuk 1 kos pribadi, bukan marketplace pencarian banyak kos).
     */
    public function landing(Request $request): View
    {
        $query = Kamar::where('status', 'tersedia')
            ->whereHas('kos', fn ($q) => $q->where('is_active', true))
            ->with(['kos', 'fotos']);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($qq) use ($search) {
                $qq->where('nomor_kamar', 'like', "%{$search}%")
                    ->orWhere('nama_kamar', 'like', "%{$search}%");
            });
        }

        return view('guest.landing', [
            'kamars' => $query->orderBy('nomor_kamar')->paginate(9)->withQueryString(),
            'search' => $request->string('q'),
            'totalKosong' => Kamar::where('status', 'tersedia')->whereHas('kos', fn ($q) => $q->where('is_active', true))->count(),
        ]);
    }

    /**
     * Guest melihat info umum kos saja (BR13). Customer yang sudah login dapat melihat
     * detail kamar per kamar dan langsung booking.
     */
    public function kosDetail(Request $request, Kos $kos): View
    {
        abort_unless($kos->is_active, 404);

        $rentangHarga = [
            'min' => Kamar::where('kos_id', $kos->id)->where('status', 'tersedia')->min('harga_bulanan'),
            'max' => Kamar::where('kos_id', $kos->id)->where('status', 'tersedia')->max('harga_bulanan'),
        ];

        $user = $request->user();
        $kamars = null;

        if ($user && $user->isCustomer()) {
            $kamars = $kos->kamars()->with('fotos')->orderBy('nomor_kamar')->get();
        }

        return view('guest.kos-detail', [
            'kos' => $kos,
            'jumlahTersedia' => $kos->kamars()->where('status', 'tersedia')->count(),
            'rentangHarga' => $rentangHarga,
            'kamars' => $kamars,
        ]);
    }

    /**
     * Modul 8: tombol "Hubungi Owner via WhatsApp" -- membuka wa.me dengan pesan siap kirim (BR08),
     * dipersonalisasi menyebutkan nomor/nama kamar yang ditanyakan jika ada.
     */
    public function hubungiOwner(Request $request, Kos $kos): RedirectResponse
    {
        $kamar = $request->filled('kamar') ? Kamar::where('kos_id', $kos->id)->find($request->integer('kamar')) : null;

        $pesan = $kamar
            ? "Halo Kak,\n\nSaya tertarik dengan Kamar {$kamar->nomor_kamar} di \"{$kos->nama}\". Boleh minta info lebih lanjut?\n\nTerima kasih."
            : "Halo Kak,\n\nSaya tertarik dengan kamar kosong yang tersedia di \"{$kos->nama}\". Boleh minta info lebih lanjut?\n\nTerima kasih.";

        return redirect()->away($kos->owner->waLink($pesan));
    }
}
