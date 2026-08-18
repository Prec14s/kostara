<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\KamarFoto;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KamarController extends Controller
{
    public function index(Request $request, Kos $kos): View
    {
        $this->authorizeOwner($kos);

        return view('owner.kamar.index', [
            'kos' => $kos,
            'kamars' => $kos->kamars()->with('fotos')->orderBy('nomor_kamar')->get(),
        ]);
    }

    public function create(Kos $kos): View
    {
        $this->authorizeOwner($kos);

        return view('owner.kamar.form', [
            'kos' => $kos,
            'kamar' => new Kamar,
            'penjagaList' => User::where('role', User::ROLE_PENJAGA)->get(),
        ]);
    }

    public function store(Request $request, Kos $kos): RedirectResponse
    {
        $this->authorizeOwner($kos);

        $data = $this->validated($request);
        $data['kos_id'] = $kos->id;

        $kamar = Kamar::create($data);

        $this->simpanFoto($request, $kamar);

        return redirect()->route('owner.kamar.edit', $kamar)
            ->with('status', 'Kamar berhasil ditambahkan. Lengkapi minimal 4 foto agar Customer lebih yakin untuk booking.');
    }

    public function edit(Kamar $kamar): View
    {
        $this->authorizeOwner($kamar->kos);

        $kamar->load('fotos');

        return view('owner.kamar.form', [
            'kos' => $kamar->kos,
            'kamar' => $kamar,
            'penjagaList' => User::where('role', User::ROLE_PENJAGA)->get(),
        ]);
    }

    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $this->authorizeOwner($kamar->kos);

        $data = $this->validated($request);
        $kamar->update($data);

        $this->simpanFoto($request, $kamar);

        return redirect()->route('owner.kos.kamar.index', $kamar->kos)->with('status', 'Data kamar berhasil diperbarui.');
    }

    /**
     * Hapus kamar. Kamar yang sedang Terisi tidak boleh dihapus langsung karena akan
     * ikut menghapus riwayat sewa & pembayaran penyewanya (cascade) -- Owner harus
     * mengosongkan/mengakhiri sewa terlebih dahulu.
     */
    public function destroy(Kamar $kamar): RedirectResponse
    {
        $this->authorizeOwner($kamar->kos);

        if ($kamar->status === 'terisi') {
            return back()->with('error', 'Kamar sedang Terisi dan tidak bisa dihapus. Akhiri/pindahkan sewa penyewa terlebih dahulu, baru hapus kamarnya.');
        }

        $kamar->delete();

        return redirect()->route('owner.kos.kamar.index', $kamar->kos)->with('status', 'Kamar berhasil dihapus.');
    }

    /**
     * Menghapus satu foto dari galeri kamar (Owner dapat mengelola foto A-H per kamar).
     */
    public function destroyFoto(Request $request, KamarFoto $kamarFoto): RedirectResponse
    {
        $this->authorizeOwner($kamarFoto->kamar->kos);

        Storage::disk('public')->delete($kamarFoto->path);
        $kamarFoto->delete();

        return back()->with('status', 'Foto berhasil dihapus.');
    }

    /**
     * Modul 5.2: Detail kartu kamar terisi -- nama, KTP, tanggal sewa, dan akumulasi pembayaran.
     */
    public function show(Request $request, Kamar $kamar): View
    {
        abort_unless((int) $kamar->kos->owner_id === (int) $request->user()->id, 403, 'Kamar ini bukan milik kos Anda.');

        $kamar->load(['kos', 'fotos', 'sewaAktif.customer', 'sewaAktif.penyewaLangsung', 'sewaAktif.pembayarans']);

        return view('owner.kamar.show', ['kamar' => $kamar]);
    }

    /**
     * Menyimpan foto-foto baru yang diunggah ke galeri kamar, melanjutkan urutan yang sudah ada.
     */
    protected function simpanFoto(Request $request, Kamar $kamar): void
    {
        if (! $request->hasFile('fotos')) {
            return;
        }

        $urutan = $kamar->fotos()->max('urutan') + 1;

        foreach ($request->file('fotos') as $file) {
            $path = $file->store('kamar', 'public');

            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => $path,
                'urutan' => $urutan++,
            ]);
        }
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'penjaga_id' => ['nullable', 'exists:users,id'],
            'nomor_kamar' => ['required', 'string', 'max:50'],
            'nama_kamar' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
            'fotos' => ['nullable', 'array', 'max:8'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'harga_harian' => ['nullable', 'numeric', 'min:0'],
            'harga_mingguan' => ['nullable', 'numeric', 'min:0'],
            'harga_bulanan' => ['nullable', 'numeric', 'min:0'],
            'harga_tahunan' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:tersedia,dipesan,terisi,maintenance,nonaktif'],
        ]);

        unset($data['fotos']);

        return $data;
    }

    protected function authorizeOwner(Kos $kos): void
    {
        $ownerId = (int) $kos->owner_id;
        $userId = (int) request()->user()->id;

        abort_unless($ownerId === $userId, 403, 'Kos ini bukan milik akun Anda.');
    }
}
