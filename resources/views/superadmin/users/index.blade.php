@extends('layouts.app')
@section('title', 'Manajemen Akun')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="font-display text-2xl font-medium text-ink">Manajemen Akun (Modul 5)</h1>
    <a href="{{ route('superadmin.users.create') }}" class="btn-primary text-sm px-3 py-2">+ Tambah Akun Baru</a>
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama/email..." class="text-sm !w-auto">
    <select name="role" class="text-sm !w-auto" onchange="this.form.submit()">
        <option value="">Semua Role</option>
        <option value="superadmin" @selected(($filters['role'] ?? '')==='superadmin')>Superadmin</option>
        <option value="owner" @selected(($filters['role'] ?? '')==='owner')>Owner</option>
        <option value="penjaga" @selected(($filters['role'] ?? '')==='penjaga')>Penjaga Kos</option>
        <option value="customer" @selected(($filters['role'] ?? '')==='customer')>Customer</option>
    </select>
    <button class="btn-ghost text-sm px-3">Cari</button>
</form>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Nama</th><th class="py-2.5 border-b border-line">Role</th><th class="py-2.5 border-b border-line">Kos</th><th class="py-2.5 border-b border-line">Status</th><th class="py-2.5 border-b border-line"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($users as $u)
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $u->name }}<div class="text-xs text-ink/35">{{ $u->email }}</div></td>
                <td class="py-3 border-b border-line/70"><x-badge color="blue">{{ ucfirst($u->role) }}</x-badge></td>
                <td class="py-3 border-b border-line/70">
                    @if ($u->role === 'owner')
                        @if ($u->kos_list_count > 0)
                            <span class="text-ink/60">{{ $u->kos_list_count }} kos</span>
                        @else
                            <span class="text-brass font-semibold">Belum punya kos</span>
                        @endif
                    @else
                        <span class="text-ink/25">-</span>
                    @endif
                </td>
                <td class="py-3 border-b border-line/70"><x-badge :color="$u->is_active ? 'green' : 'red'">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                <td class="text-right space-x-2">
                    <a href="{{ route('superadmin.users.edit', $u) }}" class="link-accent">Edit</a>
                    <form method="POST" action="{{ route('superadmin.users.toggle-active', $u) }}" class="inline">
                        @csrf @method('PATCH')
                        <button class="text-brass font-semibold hover:underline">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                        @csrf @method('DELETE')
                        <button class="text-clay font-semibold hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-4 text-center text-ink/35">Tidak ada akun ditemukan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-card>
@endsection
