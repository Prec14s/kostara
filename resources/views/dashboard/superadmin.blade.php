@extends('layouts.app')
@section('title', 'Dashboard Superadmin')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Dashboard Superadmin</h1>

<div class="grid sm:grid-cols-5 gap-4 mb-6">
    <x-stat label="Total Akun" value="{{ $totalUsers }}" />
    <x-stat label="Owner" value="{{ $totalOwner }}" accent="brass" />
    <x-stat label="Penjaga" value="{{ $totalPenjaga }}" accent="teal" />
    <x-stat label="Customer" value="{{ $totalCustomer }}" accent="sage" />
    <x-stat label="Total Kos" value="{{ $totalKos }}" accent="brass" />
</div>

<div class="flex gap-3 mb-6">
    <a href="{{ route('superadmin.kos.index') }}" class="btn-ghost text-sm">🏠 Lihat Semua Kos & Owner-nya</a>
</div>

<x-card>
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-medium">Akun Terbaru</h2>
        <a href="{{ route('superadmin.users.create') }}" class="text-sm btn-primary text-sm px-3 py-1.5">+ Tambah Akun Baru</a>
    </div>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Nama</th><th class="py-2.5 border-b border-line">Role</th><th class="py-2.5 border-b border-line">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recentUsers as $u)
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $u->name }}<div class="text-xs text-ink/35">{{ $u->email }}</div></td>
                <td class="py-3 border-b border-line/70"><x-badge color="blue">{{ ucfirst($u->role) }}</x-badge></td>
                <td class="py-3 border-b border-line/70"><x-badge :color="$u->is_active ? 'green' : 'red'">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <a href="{{ route('superadmin.users.index') }}" class="text-sm link-accent mt-3 inline-block">Lihat semua akun &rarr;</a>
</x-card>
@endsection
