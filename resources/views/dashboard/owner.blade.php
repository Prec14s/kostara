@extends('layouts.app')
@section('title', 'Dashboard Owner')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-medium text-ink">Dashboard Owner</h1>
        <p class="text-sm text-ink/45 mt-0.5">Ringkasan seluruh kos yang Anda kelola.</p>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <x-stat label="Jumlah Kos" value="{{ $stats['jumlah_kos'] }}" accent="teal" />
    <x-stat label="Jumlah Kamar" value="{{ $stats['jumlah_kamar'] }}" accent="teal" />
    <x-stat label="Kamar Tersedia" value="{{ $stats['kamar_tersedia'] }}" accent="sage" />
    <x-stat label="Kamar Terisi" value="{{ $stats['kamar_terisi'] }}" accent="brass" />
    <x-stat label="Booking Baru" value="{{ $stats['booking_baru'] }}" accent="brass" />
    <x-stat label="Sewa Aktif" value="{{ $stats['sewa_aktif'] }}" accent="sage" />
    <x-stat label="Menunggu Verifikasi" value="{{ $stats['pembayaran_menunggu'] }}" accent="clay" />
    <x-stat label="Jatuh Tempo 7 Hari" value="{{ $stats['jatuh_tempo'] }}" accent="clay" />
</div>

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="font-display text-lg font-medium text-ink">Status Kamar</h2>
        <p class="text-xs text-ink/45 mt-0.5">Klik kamar berstatus <strong class="text-ink/70">Terisi</strong> untuk melihat detail penyewa.</p>
    </div>
    <div class="hidden sm:flex items-center gap-3 text-xs text-ink/45">
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-brass"></span> Terisi</span>
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sage"></span> Tersedia</span>
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-ink/20"></span> Lainnya</span>
    </div>
</div>

<div class="grid sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse ($kamars as $kamar)
        @php
            $isTerisi = $kamar->status === 'terisi';
            $dot = match($kamar->status) {
                'terisi' => 'bg-brass',
                'tersedia' => 'bg-sage',
                'dipesan' => 'bg-teal',
                'maintenance' => 'bg-clay',
                default => 'bg-ink/20',
            };
            $statusLabel = match($kamar->status) {
                'terisi' => 'Terisi',
                'tersedia' => 'Tersedia',
                'dipesan' => 'Dipesan',
                'maintenance' => 'Maintenance',
                default => 'Tidak Aktif',
            };
        @endphp

        @if ($isTerisi)
            <a href="{{ route('owner.kamar.show', $kamar) }}"
               class="group relative surface p-4 pt-6 hover:-translate-y-1 hover:shadow-lift hover:border-brass/40 transition-all duration-200">
                <div class="absolute -top-2 left-4"><x-room-tag :nomor="$kamar->nomor_kamar" /></div>
                <p class="text-xs text-ink/40 mt-2">{{ $kamar->kos->nama }}</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                    <span class="text-xs font-semibold text-ink/70">{{ $statusLabel }}</span>
                </div>
                <p class="text-xs font-semibold text-brass mt-3 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                    Lihat detail <span aria-hidden="true">&rarr;</span>
                </p>
            </a>
        @else
            <div class="surface p-4 pt-6 relative opacity-80">
                <div class="absolute -top-2 left-4">
                    <div class="inline-flex items-center gap-1.5 -rotate-2 bg-white border border-line text-ink/50 text-xs font-mono font-semibold px-2.5 py-1 rounded-md shadow-sm">
                        {{ $kamar->nomor_kamar }}
                    </div>
                </div>
                <p class="text-xs text-ink/40 mt-2">{{ $kamar->kos->nama }}</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                    <span class="text-xs font-semibold text-ink/50">{{ $statusLabel }}</span>
                </div>
            </div>
        @endif
    @empty
        <div class="col-span-full">
            <x-empty title="Belum ada kamar" subtitle="Tambahkan kos dan kamar pertama Anda untuk mulai menyewakan.">
                <x-slot:action>
                    <a href="{{ route('owner.kos.index') }}" class="btn-primary text-sm">+ Tambah Kos & Kamar</a>
                </x-slot:action>
            </x-empty>
        </div>
    @endforelse
</div>
@endsection
