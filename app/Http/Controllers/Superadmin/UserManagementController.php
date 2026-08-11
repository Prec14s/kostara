<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Modul 5: Superadmin dapat melihat daftar seluruh akun pada sistem.
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('superadmin.users.index', [
            'users' => $query->withCount('kosList')->paginate(15)->withQueryString(),
            'filters' => $request->only(['role', 'q']),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.users.create');
    }

    /**
     * Superadmin dapat membuat akun untuk seluruh role, termasuk Superadmin lain (BR15).
     * Khusus role Owner, Superadmin juga dapat langsung membuatkan kos pertamanya
     * sehingga Owner dapat langsung mengelola kos & kamarnya begitu login pertama kali.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_OWNER, User::ROLE_PENJAGA, User::ROLE_CUSTOMER])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // Wajib untuk Owner & Penjaga -- dipakai untuk mengirim link WhatsApp pengingat pembayaran ke Customer.
            'phone' => ['required_if:role,'.User::ROLE_OWNER.','.User::ROLE_PENJAGA, 'nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            // Data kos awal, hanya relevan jika role Owner. Boleh dikosongkan, Owner bisa menambahkan sendiri nanti.
            'nama_kos' => ['nullable', 'required_with:alamat_kos', 'string', 'max:255'],
            'alamat_kos' => ['nullable', 'required_with:nama_kos', 'string'],
        ], [
            'phone.required_if' => 'Nomor WhatsApp wajib diisi untuk role Owner dan Penjaga Kos.',
            'nama_kos.required_with' => 'Nama kos wajib diisi jika alamat kos sudah diisi.',
            'alamat_kos.required_with' => 'Alamat kos wajib diisi jika nama kos sudah diisi.',
        ]);

        $kosData = [
            'nama_kos' => $data['nama_kos'] ?? null,
            'alamat_kos' => $data['alamat_kos'] ?? null,
        ];
        unset($data['nama_kos'], $data['alamat_kos']);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        $user = DB::transaction(function () use ($data, $kosData) {
            $user = User::create($data);

            if ($user->role === User::ROLE_OWNER && filled($kosData['nama_kos'])) {
                Kos::create([
                    'owner_id' => $user->id,
                    'nama' => $kosData['nama_kos'],
                    'alamat' => $kosData['alamat_kos'],
                    'is_active' => true,
                ]);
            }

            return $user;
        });

        $status = 'Akun baru berhasil dibuat.';
        if ($user->role === User::ROLE_OWNER) {
            $status .= filled($kosData['nama_kos'])
                ? ' Kos "'.$kosData['nama_kos'].'" sudah dibuat dan siap dikelola Owner.'
                : ' Owner belum punya kos -- ia bisa menambahkan sendiri lewat menu Kos Saya.';
        }

        return redirect()->route('superadmin.users.index')->with('status', $status);
    }

    public function edit(User $user): View
    {
        return view('superadmin.users.edit', [
            'userAccount' => $user,
            'kosList' => $user->role === User::ROLE_OWNER ? $user->kosList()->withCount('kamars')->latest()->get() : collect(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_OWNER, User::ROLE_PENJAGA, User::ROLE_CUSTOMER])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required_if:role,'.User::ROLE_OWNER.','.User::ROLE_PENJAGA, 'nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            // Tambah kos baru untuk Owner yang belum punya kos sama sekali.
            'nama_kos' => ['nullable', 'required_with:alamat_kos', 'string', 'max:255'],
            'alamat_kos' => ['nullable', 'required_with:nama_kos', 'string'],
        ], [
            'phone.required_if' => 'Nomor WhatsApp wajib diisi untuk role Owner dan Penjaga Kos.',
            'nama_kos.required_with' => 'Nama kos wajib diisi jika alamat kos sudah diisi.',
            'alamat_kos.required_with' => 'Alamat kos wajib diisi jika nama kos sudah diisi.',
        ]);

        $kosData = [
            'nama_kos' => $data['nama_kos'] ?? null,
            'alamat_kos' => $data['alamat_kos'] ?? null,
        ];
        unset($data['nama_kos'], $data['alamat_kos']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::transaction(function () use ($data, $kosData, $user) {
            $user->update($data);

            if ($user->role === User::ROLE_OWNER && filled($kosData['nama_kos']) && $user->kosList()->count() === 0) {
                Kos::create([
                    'owner_id' => $user->id,
                    'nama' => $kosData['nama_kos'],
                    'alamat' => $kosData['alamat_kos'],
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('superadmin.users.index')->with('status', 'Data akun berhasil diperbarui.');
    }

    /**
     * Menonaktifkan / mengaktifkan kembali akun (Modul 5: Kewenangan Superadmin).
     */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', $user->is_active ? 'Akun diaktifkan kembali.' : 'Akun dinonaktifkan.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')->with('status', 'Akun berhasil dihapus.');
    }
}
