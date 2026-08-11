<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\PenyewaLangsung;
use App\Models\Sewa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenyewaLangsungController extends Controller
{
    /**
     * Modul 9 & Alur 29: Owner menginput penyewa yang datang langsung tanpa akun.
     */
    public function create(Request $request): View
    {
        $kamarTersedia = Kamar::whereHas('kos', fn ($q) => $q->where('owner_id', $request->user()->id))
            ->where('status', 'tersedia')
            ->with('kos')
            ->get();

        return view('owner.sewa-langsung.create', ['kamarTersedia' => $kamarTersedia]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kamar_id' => ['required', 'exists:kamars,id'],
            'nama' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'no_ktp' => ['nullable', 'string', 'max:50'],
            'foto_ktp' => ['nullable', 'image', 'max:4096'],
            'alamat' => ['nullable', 'string'],
            'jenis_durasi' => ['required', 'in:harian,mingguan,bulanan,tahunan'],
            'tanggal_mulai' => ['required', 'date'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $kamar = Kamar::findOrFail($data['kamar_id']);
        abort_unless((int) $kamar->kos->owner_id === (int) $request->user()->id, 403, 'Kamar ini bukan milik kos Anda.');

        DB::transaction(function () use ($data, $kamar, $request) {
            if ($request->hasFile('foto_ktp')) {
                // Disimpan di disk privat sesuai BR05 -- tidak bisa diakses langsung lewat URL publik.
                $data['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'private');
            }

            $penyewa = PenyewaLangsung::create([
                'kamar_id' => $kamar->id,
                'input_by' => $request->user()->id,
                'nama' => $data['nama'],
                'whatsapp' => $data['whatsapp'],
                'no_ktp' => $data['no_ktp'] ?? null,
                'foto_ktp' => $data['foto_ktp'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $tanggalMulai = \Carbon\Carbon::parse($data['tanggal_mulai']);
            $tanggalSelesai = match ($data['jenis_durasi']) {
                'harian' => $tanggalMulai->copy()->addDay(),
                'mingguan' => $tanggalMulai->copy()->addWeek(),
                'bulanan' => $tanggalMulai->copy()->addMonth(),
                'tahunan' => $tanggalMulai->copy()->addYear(),
            };

            Sewa::create([
                'kamar_id' => $kamar->id,
                'penyewa_langsung_id' => $penyewa->id,
                'jenis_durasi' => $data['jenis_durasi'],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'harga' => $kamar->hargaUntuk($data['jenis_durasi']) ?? 0,
                'deposit' => $data['deposit'] ?? 0,
                'status' => 'aktif',
            ]);

            $kamar->update(['status' => 'terisi']);
        });

        return redirect()->route('owner.dashboard')->with('status', 'Sewa langsung berhasil disimpan. Kamar kini berstatus Terisi.');
    }
}
