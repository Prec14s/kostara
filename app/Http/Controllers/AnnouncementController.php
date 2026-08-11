<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Modul 24: pengumuman untuk penghuni kos.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isOwner()) {
            $announcements = Announcement::whereIn('kos_id', $user->kosList()->pluck('id'))->latest()->get();
        } elseif ($user->isPenjaga()) {
            $kosIds = $user->kamarDijaga()->pluck('kos_id');
            $announcements = Announcement::whereIn('kos_id', $kosIds)->latest()->get();
        } else {
            $kosIds = $user->sewas()->where('status', 'aktif')->with('kamar')->get()->pluck('kamar.kos_id');
            $announcements = Announcement::whereIn('kos_id', $kosIds)->latest()->get();
        }

        return view('announcements.index', ['announcements' => $announcements]);
    }

    public function create(Request $request): View
    {
        return view('announcements.create', ['kosList' => $request->user()->kosList()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kos_id' => ['required', 'exists:kos,id'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
        ]);

        $kos = Kos::findOrFail($data['kos_id']);
        abort_unless((int) $kos->owner_id === (int) $request->user()->id, 403, 'Kos ini bukan milik akun Anda.');

        $data['owner_id'] = $request->user()->id;
        Announcement::create($data);

        return redirect()->route('announcements.index')->with('status', 'Pengumuman berhasil dipublikasikan.');
    }
}
