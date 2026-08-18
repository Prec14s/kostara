<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KosController extends Controller
{
    public function index(Request $request): View
    {
        return view('owner.kos.index', [
            'kosList' => $request->user()->kosList()->withCount('kamars')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('owner.kos.form', ['kos' => new Kos]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['owner_id'] = $request->user()->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kos', 'public');
        }

        Kos::create($data);

        return redirect()->route('owner.kos.index')->with('status', 'Kos berhasil ditambahkan.');
    }

    public function edit(Kos $kos): View
    {
        $this->authorizeOwner($kos);

        return view('owner.kos.form', ['kos' => $kos]);
    }

    public function update(Request $request, Kos $kos): RedirectResponse
    {
        $this->authorizeOwner($kos);

        $data = $this->validated($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kos', 'public');
        }

        $kos->update($data);

        return redirect()->route('owner.kos.index')->with('status', 'Data kos berhasil diperbarui.');
    }

    public function destroy(Kos $kos): RedirectResponse
    {
        $this->authorizeOwner($kos);
        $kos->delete();

        return back()->with('status', 'Kos berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'alamat' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'fasilitas' => ['nullable', 'string'],
            'peraturan' => ['nullable', 'string'],
            'jam_operasional' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    protected function authorizeOwner(Kos $kos): void
    {
        $ownerId = (int) $kos->owner_id;
        $userId = (int) request()->user()->id;

        abort_unless($ownerId === $userId, 403, 'Kos ini bukan milik akun Anda.');
    }
}
