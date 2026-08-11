<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    /**
     * Modul 14/15: form laporan maintenance hanya tersedia pukul 07.00-17.00 (BR06).
     */
    public function create(Request $request): View
    {
        $jamSekarang = (int) now()->format('H');
        $bukaLayanan = $jamSekarang >= 7 && $jamSekarang < 17;

        $kamarAktif = $request->user()->sewas()->where('status', 'aktif')->with('kamar')->get();

        return view('maintenance.create', [
            'bukaLayanan' => $bukaLayanan,
            'kamarAktif' => $kamarAktif,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $jamSekarang = (int) now()->format('H');
        abort_unless($jamSekarang >= 7 && $jamSekarang < 17, 422, 'Layanan maintenance hanya tersedia pukul 07.00-17.00.');

        $data = $request->validate([
            'kamar_id' => ['required', 'exists:kamars,id'],
            'jenis_masalah' => ['required', 'in:ac_rusak,lampu_rusak,keran_toilet,ganti_sprei,bersihkan_kamar,pintu_kunci,listrik,wifi,perabot_rusak,hama_serangga,lainnya'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:4096'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('maintenance', 'public');
        }

        $data['customer_id'] = $request->user()->id;
        $data['status'] = 'menunggu';

        MaintenanceReport::create($data);

        return redirect()->route('maintenance.index')->with('status', 'Laporan maintenance berhasil dikirim.');
    }

    /**
     * Modul 16/29: Owner & Penjaga melihat dan memproses laporan maintenance.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = MaintenanceReport::with(['kamar.kos', 'customer', 'penjaga']);

        if ($user->isOwner()) {
            $query->whereHas('kamar.kos', fn ($q) => $q->where('owner_id', $user->id));
        } elseif ($user->isPenjaga()) {
            $query->whereHas('kamar', fn ($q) => $q->where('penjaga_id', $user->id));
        } elseif ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        return view('maintenance.index', ['reports' => $query->latest()->paginate(15)]);
    }

    public function updateStatus(Request $request, MaintenanceReport $maintenanceReport): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:menunggu,diproses,selesai,ditolak']]);

        $maintenanceReport->update([
            'status' => $data['status'],
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Status maintenance diperbarui.');
    }
}
