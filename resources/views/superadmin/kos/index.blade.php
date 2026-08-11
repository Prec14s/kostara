@extends('layouts.app')
@section('title', 'Semua Kos')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-2xl font-medium text-ink">Semua Kos</h1>
    <p class="text-sm text-ink/45 mt-0.5">Daftar seluruh kos yang terdaftar di sistem beserta Owner pemiliknya.</p>
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama kos, alamat, atau nama/email Owner..." class="max-w-md">
    <button class="btn-ghost text-sm px-4">Cari</button>
</form>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Kos</th>
                <th class="py-2.5 border-b border-line">Owner</th>
                <th class="py-2.5 border-b border-line">Kamar</th>
                <th class="py-2.5 border-b border-line">Status</th>
                <th class="py-2.5 border-b border-line">ID</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($kosList as $kos)
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">
                    <p class="font-semibold text-ink">{{ $kos->nama }}</p>
                    <p class="text-xs text-ink/40">{{ $kos->alamat }}</p>
                </td>
                <td class="py-3 border-b border-line/70">
                    @if ($kos->owner)
                        <p class="font-medium text-ink">{{ $kos->owner->name }}</p>
                        <p class="text-xs text-ink/40 font-mono">{{ $kos->owner->email }}</p>
                    @else
                        <span class="text-ink/35 italic">Owner tidak ditemukan</span>
                    @endif
                </td>
                <td class="py-3 border-b border-line/70">{{ $kos->kamars_count }} kamar</td>
                <td class="py-3 border-b border-line/70">
                    <x-badge color="{{ $kos->is_active ? 'green' : 'gray' }}">{{ $kos->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge>
                </td>
                <td class="py-3 border-b border-line/70 font-mono text-xs text-ink/35">#{{ $kos->id }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-8 text-center text-ink/35">Belum ada kos yang terdaftar.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $kosList->links() }}</div>
</x-card>
@endsection
