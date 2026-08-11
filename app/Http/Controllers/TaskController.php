<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Modul 18: Owner memberi tugas kepada Penjaga; Penjaga melihat & memperbarui status.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Task::with(['owner', 'penjaga', 'kamar']);

        if ($user->isOwner()) {
            $query->where('owner_id', $user->id);
        } elseif ($user->isPenjaga()) {
            $query->where('penjaga_id', $user->id);
        }

        return view('tasks.index', ['tasks' => $query->latest()->paginate(15)]);
    }

    public function create(Request $request): View
    {
        $kosIds = $request->user()->kosList()->pluck('id');

        return view('tasks.create', [
            'penjagaList' => User::where('role', User::ROLE_PENJAGA)
                ->whereHas('kamarDijaga', fn ($q) => $q->whereIn('kos_id', $kosIds))
                ->get(),
            'kamarList' => \App\Models\Kamar::whereIn('kos_id', $kosIds)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'penjaga_id' => ['required', 'exists:users,id'],
            'kamar_id' => ['nullable', 'exists:kamars,id'],
            'judul' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'deadline' => ['nullable', 'date'],
        ]);

        $data['owner_id'] = $request->user()->id;
        $data['status'] = 'belum_dikerjakan';

        Task::create($data);

        return redirect()->route('tasks.index')->with('status', 'Tugas berhasil diberikan ke Penjaga.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->penjaga_id === $request->user()->id, 403);

        $data = $request->validate(['status' => ['required', 'in:belum_dikerjakan,diproses,selesai']]);
        $task->update($data);

        return back()->with('status', 'Status tugas diperbarui.');
    }
}
