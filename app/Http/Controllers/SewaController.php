<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SewaController extends Controller
{
    /**
     * Modul 11 & 12: daftar sewa, disaring berdasarkan role yang mengakses.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Sewa::with(['kamar.kos', 'customer', 'penyewaLangsung', 'perpanjanganMenunggu']);

        if ($user->isOwner()) {
            $query->whereHas('kamar.kos', fn ($q) => $q->where('owner_id', $user->id));
        } elseif ($user->isPenjaga()) {
            $query->whereHas('kamar', fn ($q) => $q->where('penjaga_id', $user->id));
        } elseif ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        return view('sewa.index', ['sewas' => $query->latest()->paginate(15)]);
    }

    /**
     * Owner dapat menghapus data sewa (mis. salah input, dibatalkan) yang berkaitan
     * dengan kos miliknya. Riwayat pembayaran pada sewa ini ikut terhapus (cascade),
     * dan kamar dikembalikan ke status Tersedia jika sebelumnya terisi oleh sewa ini.
     */
    public function destroy(Request $request, Sewa $sewa): RedirectResponse
    {
        abort_unless((int) $sewa->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Sewa ini bukan milik kos Anda.');

        $kamar = $sewa->kamar;

        $sewa->delete();

        if (in_array($kamar->status, ['terisi', 'dipesan']) && ! $kamar->sewaAktif()->exists()) {
            $kamar->update(['status' => 'tersedia']);
        }

        return back()->with('status', 'Data sewa berhasil dihapus.');
    }
}
